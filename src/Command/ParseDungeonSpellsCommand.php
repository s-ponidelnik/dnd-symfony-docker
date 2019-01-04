<?php

namespace App\Command;

use App\Entity\Enum\SpellAreaSizeType;
use App\Entity\Enum\SpellAreaType;
use App\Entity\Enum\SpellCastingTimeType;
use App\Entity\Enum\SpellDurationType;
use App\Entity\Enum\SpellRangeType;
use App\Entity\Spell;
use App\Repository\CharacterClassRepository;
use App\Repository\SourceRepository;
use App\Repository\SpellRepository;
use App\Repository\SpellSchoolRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class ParseDungeonSpellsCommand extends ContainerAwareCommand
{
    private $spellSchoolRepository;
    private $characterClassRepository;
    private $sourceRepository;
    private $spellRepository;
    protected static $defaultName = 'parse:dungeon-spells';

    public function __construct(SpellSchoolRepository $spellSchoolRepository, CharacterClassRepository $characterClassRepository, SourceRepository $sourceRepository, SpellRepository $spellRepository)
    {
        $this->spellSchoolRepository = $spellSchoolRepository;
        $this->characterClassRepository = $characterClassRepository;
        $this->sourceRepository = $sourceRepository;
        $this->spellRepository = $spellRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    private function getCache(string $name): ?string
    {
        if (file_exists(__DIR__ . '/../../var/cache/parse_' . $name . '.cache')) {
            return file_get_contents(__DIR__ . '/../../var/cache/parse_' . $name . '.cache');
        }
        return null;
    }

    private function setCache(string $name, string $data)
    {
        file_put_contents(__DIR__ . '/../../var/cache/parse_' . $name . '.cache', $data);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }
        $clearSpellDataList = [];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://dungeon.su/spells/');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $spellList = $this->getCache('spell_list');
        if (!$spellList) {
            $spellList = curl_exec($curl);
            $spellList = preg_replace("/\n|\r|\t/", '', $spellList);
            $this->setCache('spell_list', $spellList);
        }

        preg_match_all('/<a href="\/spells\/(([0-9]*)-(.*))\/"/U', $spellList, $all);
        foreach ($all[1] as $spellName) {
            $data = [];
            $spellData = $this->getCache('spell_' . $spellName);
            if (!$spellData) {
                $url = 'http://dungeon.su/spells/' . $spellName . '/';
                curl_setopt($curl, CURLOPT_URL, $url);
                $spellData = curl_exec($curl);
                $spellData = preg_replace("/\n|\r|\t/", '', $spellData);
                $this->setCache('spell_' . $spellName, $spellData);
            }
            preg_match('/<div itemprop="description">(.*)<\/div>/U', $spellData, $description);
            $data['description'] = strip_tags(ltrim($description[1]));
            preg_match('/Уровень:<\/strong>(.*)<\/li>/U', $spellData, $level);
            $data['level'] = (int)ltrim($level[1]);
            preg_match('/Время накладывания:<\/strong>(.*)<\/li>/U', $spellData, $casting_time);
            $data['casting_time'] = ltrim($casting_time[1]);
            preg_match('/Школа:<\/strong>(.*)<\/li>/U', $spellData, $school);
            $data['school'] = ltrim($school[1]);
            preg_match('/Дистанция:<\/strong>(.*)<\/li>/U', $spellData, $range);
            $data['range'] = ltrim($range[1]);
            preg_match('/Компоненты:<\/strong>(.*)<\/li>/U', $spellData, $components);
            $data['components'] = ltrim($components[1]);
            preg_match('/Длительность:<\/strong>(.*)<\/li>/U', $spellData, $duration);
            $data['duration'] = ltrim($duration[1]);
            preg_match('/Классы:<\/strong>(.*)<\/li>/U', $spellData, $classes);
            $data['classes'] = ltrim($classes[1]);
            preg_match('/Источник:<\/strong>(.*)<\/li>/U', $spellData, $source);
            if (!empty($source)) {
                $data['source'] = str_replace(['»', '«'], '', strip_tags(ltrim($source[1])));
            } else {
                preg_match('/Источники:<\/strong>(.*)<\/li>/U', $spellData, $source);
                $data['source'] = str_replace(['»', '«', '"'], '', strip_tags(ltrim($source[1])));
            }
            preg_match('/<a href=".*" class="page-item">(.*)<\/a>/U', $spellData, $name);
            $data['name'] = ltrim($name[1]);
            $data['identifier'] = substr($spellName, strpos($spellName, '-') + 1, strlen($spellName));
            $school = $this->spellSchoolRepository->findByRuName($data['school']);
            if (!is_object($school)) {
                throw new \Exception('Unknown school: ' . $data['school']);
            } else {
                $data['school_raw'] = $data['school'];
                $data['school'] = $school->getIdentifier();
            }
            $data['schoolEn'] = $school->getNameEn();

            if (strpos($data['source'], 'Homebrew') !== false) {
                preg_match('/<p><strong>Источник<\/strong>:(.*)<\/p>/U', $spellData, $source);
                if (!empty($source)) {
                    $spellData = preg_replace('/<p><strong>Источник<\/strong>:(.*)<\/p>/U', '', $spellData);
                    preg_match('/<div itemprop="description">(.*)<\/div>/U', $spellData, $description);
                    $data['description'] = strip_tags(ltrim($description[1]));
                    $data['raw_source'] = $data['source'];
                    $data['source'] = ltrim($source[1]);
                } else {
                    preg_match('/<div itemprop="description"><p><strong>Источник:(.*)<\/strong>/U', $spellData, $source);
                    if (!empty($source)) {
                        $spellData = preg_replace('/<p><strong>Источник:(.*)<\/strong><\/p>/U', '', $spellData);
                        preg_match('/<div itemprop="description">(.*)<\/div>/U', $spellData, $description);
                        $data['description'] = strip_tags(ltrim($description[1]));
                        $data['raw_source'] = $data['source'];
                        $data['source'] = ltrim($source[1]);
                    }
                }
                $data['source'] = str_replace(['»', '«'], '', strip_tags(ltrim($data['source'])));
            }
            if ($data['classes'] == 'Любой класс') {
                $data['classes_raw'] = $data['classes'];
                $data['classes'] = [];
                foreach ($this->characterClassRepository->findAll() as $class) {
                    $data['classes'][] = $class->getIdentifier();
                }
            } else {
                $data['classes_raw'] = $data['classes'];
                $classes = explode(',', $data['classes']);
                $data['classes'] = [];
                foreach ($classes as $className) {
                    $class = $this->characterClassRepository->findByRuName(ltrim($className));
                    if (!is_object($class)) {
                        throw new \Exception('Unknown class: ' . $className);
                    }
                    $data['classes'][] = $class->getIdentifier();
                }
            }
            if (!isset($data['raw_source']) && $data['source'] == 'Homebrew / Карточка от сообщества сайта') {
                $data['source'] = 'dungeon.su';
            } elseif (!isset($data['raw_source']) && strpos($data['source'], 'Homebrew') !== false) {
                $data['source'] = ltrim(substr($data['source'], strpos($data['source'], '/') + 1, strlen($data['source'])));

            }
            if ($data['identifier'] == 'magic_shield') {
//                var_dump($data['raw_source']);
//                die();
            }

            $data['raw_source'] = $data['source'];
            $sources = explode(',', $data['source']);
            $data['source'] = [];
            foreach ($sources as $sourceName) {
                $sourceName = ltrim(str_replace('"', '', $sourceName));
                $source = $this->sourceRepository->findByName($sourceName);
                if (!is_object($source)) {
                    throw new \Exception('Unknown source: ' . $sourceName);
                } else {
                    $data['source'][] = $source->getName();
                }
            }

            $data['casting_time_description_ru'] = null;
            $data['is_ritual'] = false;
            $data['casting_time'] = rtrim(ltrim(str_replace('"', '', $data['casting_time'])));
            $data['raw_casting_time'] = $data['casting_time'];
            if (strpos($data['casting_time'], '(ритуал)') !== false) {

                $data['casting_time'] = rtrim(str_replace('(ритуал)', '', $data['casting_time']));
            }
            if ($data['casting_time'] == 'Действие' || $data['casting_time'] == '1 Действие') {
                $data['casting_time_type'] = SpellCastingTimeType::ACTION;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 действие') {
                $data['casting_time_type'] = SpellCastingTimeType::ACTION;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 бонусное действие' || $data['casting_time'] == 'Бонусное действие') {
                $data['casting_time_type'] = SpellCastingTimeType::BONUS_ACTION;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 минута') {
                $data['casting_time_type'] = SpellCastingTimeType::MINUTE;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 час') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '3 часа') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 3;
            } elseif ($data['casting_time'] == '24 часа') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 24;
            } elseif ($data['casting_time'] == '8 часов') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 8;
            } elseif ($data['casting_time'] == '12 часов') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 12;
            } elseif ($data['casting_time'] == 'Реакция') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '10 минут') {
                $data['casting_time_type'] = SpellCastingTimeType::MINUTE;
                $data['casting_time'] = 10;
            } elseif ($data['casting_time'] == '1 раунд') {
                $data['casting_time_type'] = SpellCastingTimeType::ROUND;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == 'Ритуал в 1 час') {
                $data['casting_time_type'] = SpellCastingTimeType::HOUR;
                $data['casting_time'] = 1;
                $data['is_ritual'] = true;
            } elseif ($data['casting_time'] == '1 действие или 8 часов') {
                $data['casting_time_type'] = SpellCastingTimeType::ACTION;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 реакция, совершаемая вами, когда вы видите, как существо в пределах 60 фт. от вас накладывает заклинание') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, совершаемая вами, когда вы видите, как существо в пределах 60 фт. от вас накладывает заклинание';
            } elseif ($data['casting_time'] == '1 реакция, совершаемая вами, когда вы или существо в пределах 60 фт. от вас начинаете падать') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, совершаемая вами, когда вы или существо в пределах 60 фт. от вас начинаете падать';
            } elseif ($data['casting_time'] == 'Ритуал (1 действие)') {
                $data['casting_time_type'] = SpellCastingTimeType::ACTION;
                $data['casting_time'] = 1;
                $data['is_ritual'] = true;
            } elseif ($data['casting_time'] == '1 реакция, которую вы совершаете, получив урон звуком, кислотой, огнём, холодом или электричеством') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, которую вы совершаете, получив урон звуком, кислотой, огнём, холодом или электричеством';
            } elseif ($data['casting_time'] == '5 минут') {
                $data['casting_time_type'] = SpellCastingTimeType::MINUTE;
                $data['casting_time'] = 5;
            } elseif ($data['casting_time'] == '5 раундов (30 секунд)') {
                $data['casting_time_type'] = SpellCastingTimeType::ROUND;
                $data['casting_time'] = 1;
            } elseif ($data['casting_time'] == '1 реакция, совершаемая, когда по вам попадает атака или вы становитесь целью волшебной стрелы') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, совершаемая, когда по вам попадает атака или вы становитесь целью волшебной стрелы';
            } elseif ($data['casting_time'] == '1 реакция, которую вы используете, когда умирает гуманоид, которого вы можете видеть в пределах 60 фт. от себя') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, которую вы используете, когда умирает гуманоид, которого вы можете видеть в пределах 60 фт. от себя';
            } elseif ($data['casting_time'] == '1 реакция, совершаемая вами в ответ на получение урона от существа, находящегося в пределах 60 фт. от вас и видимого вами') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция, совершаемая вами в ответ на получение урона от существа, находящегося в пределах 60 фт. от вас и видимого вами';
            } elseif ($data['casting_time'] == 'Реакция на начало боя') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция на начало боя';
            } elseif ($data['casting_time'] == 'Реакция на атаку по союзнику') {
                $data['casting_time_type'] = SpellCastingTimeType::REACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Реакция на атаку по союзнику';
            } elseif ($data['casting_time'] == '1 бонусное действие (сразу после нанесения раны врагу в ближнем бою)') {
                $data['casting_time_type'] = SpellCastingTimeType::BONUS_ACTION;
                $data['casting_time'] = 1;
                $data['casting_time_description_ru'] = 'Бонусное действие, сразу после нанесения раны врагу в ближнем бою';
            } elseif ($data['casting_time'] == 'ритуал в 1 минуту') {
                $data['casting_time_type'] = SpellCastingTimeType::MINUTE;
                $data['casting_time'] = 1;
                $data['is_ritual'] = true;
            } else {
                $data['casting_time'] = null;
            }
            if (is_null($data['casting_time'])) {
                throw new \Exception('Unknown casting time: ' . $data['raw_casting_time']);
            }

            $data['concentration'] = false;
            $data['duration_raw'] = $data['duration'];
            if (strpos($data['duration'], 'Концентрация,') !== false) {
                $data['duration'] = ltrim(str_replace('Концентрация,', '', $data['duration']));
                $data['concentration'] = true;
            }
            $data['duration'] = rtrim($data['duration']);
            if ($data['duration'] == '1 час' || $data['duration'] == 'Вплоть до 1 часа') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 1;
            } elseif ($data['duration'] == 'мгновенно' || $data['duration'] == 'Мгновенная' || $data['duration'] == 'Мгновенно' || $data['duration'] == 'Моментальная') {
                $data['duration_type'] = SpellDurationType::INSTANTANEOUS;
                $data['duration'] = null;
            } elseif ($data['duration'] == '10 дней') {
                $data['duration_type'] = SpellDurationType::DAY;
                $data['duration'] = 10;
            } elseif ($data['duration'] == 'вплоть до 1 минуты' || $data['duration'] == 'вплоть до 1 мин.') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 1;
            } elseif ($data['duration'] == 'вплоть до 10 минут') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 10;
            } elseif ($data['duration'] == 'вплоть до 1 часа') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 1;
            } elseif ($data['duration'] == '1 минута' || $data['duration'] == '1 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 1;
            } elseif ($data['duration'] == '24 часа' || $data['duration'] == 'вплоть до 24 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 24;
            } elseif ($data['duration'] == 'Вплоть до 8 часов' || $data['duration'] == '8 часов' || $data['duration'] == 'вплоть до 8 часов' || $data['duration'] == 'до 8 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 8;
            } elseif ($data['duration'] == 'До срабатывания' || $data['duration'] == 'До рассеивания' || $data['duration'] == 'До использования' || $data['duration'] == 'Постоянная, пока не выполнится условие' || $data['duration'] == 'Постоянная' || $data['duration'] == 'До уничтожения' || $data['duration'] == 'До смерти Ихора' || $data['duration'] == 'Пока существо не выберется' || $data['duration'] == 'Без ограничения' || $data['duration'] == 'До снятия' || $data['duration'] == 'Пока не рассеется' || $data['duration'] == 'Пока не рассеется или не сработает') {
                $data['duration_type'] = SpellDurationType::UNTIL_DISSIPATES;
            } elseif ($data['duration'] == 'Мгновенная или 1 час') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 1;
            } elseif ($data['duration'] == '1 раунд' || $data['duration'] == 'вплоть до 1 раунда') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 1;
            } elseif ($data['duration'] == 'вплоть до 2 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 2;
            } elseif ($data['duration'] == '10 минут') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 10;
            } elseif ($data['duration'] == '1 день' || $data['duration'] == 'вплоть до 1 дня' || $data['duration'] == 'Вплоть до 1 дня') {
                $data['duration_type'] = SpellDurationType::DAY;
                $data['duration'] = 1;
            } elseif ($data['duration'] == '7 дней') {
                $data['duration_type'] = SpellDurationType::DAY;
                $data['duration'] = 7;
            } elseif ($data['duration'] == '30 дней') {
                $data['duration_type'] = SpellDurationType::DAY;
                $data['duration'] = 30;
            } elseif ($data['duration'] == 'вплоть до 6 раундов') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 6;
            } elseif ($data['duration'] == 'Вплоть до 1 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 1;
            } elseif ($data['duration'] == '3 раунда' || $data['duration'] == 'до 3 раундов') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 3;
            } elseif ($data['duration'] == '5 раундов') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 5;
            } elseif ($data['duration'] == 'Концентрация до 10 мин.') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 10;
                $data['concentration'] = true;
            } elseif ($data['duration'] == 'Концентрация вплоть до 1 мин.' || $data['duration'] == 'концентрация до 1 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 1;
                $data['concentration'] = true;
            } elseif ($data['duration'] == 'вплоть до 10 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 10;
            } elseif ($data['duration'] == '10 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 10;
            } elseif ($data['duration'] == 'До следующего хода, включительно') {
                $data['duration_type'] = SpellDurationType::SECOND;
                $data['duration'] = 6;
            } elseif ($data['duration'] == '5 минут') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 5;
            } elseif ($data['duration'] == 'До 6 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 6;
            } elseif ($data['duration'] == '4 часа') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 4;
            } elseif ($data['duration'] == '2 часа') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 2;
            } elseif ($data['duration'] == '2 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 2;
            } elseif ($data['duration'] == '3 суток') {
                $data['duration_type'] = SpellDurationType::DAY;
                $data['duration'] = 3;
            } elseif ($data['duration'] == 'Вплоть до 4 часов') {
                $data['duration_type'] = SpellDurationType::HOUR;
                $data['duration'] = 4;
            } elseif ($data['duration'] == '4 раунда') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 4;
            } elseif ($data['duration'] == '2 раунда') {
                $data['duration_type'] = SpellDurationType::ROUND;
                $data['duration'] = 2;
            } elseif ($data['duration'] == 'До разрушения предмета') {
                $data['duration_type'] = SpellDurationType::UNTIL_DISSIPATES;
            } elseif ($data['duration'] == 'Концентрация до 1 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 1;
                $data['concentration'] = true;
            } elseif ($data['duration'] == 'концентрация до 10 минуты') {
                $data['duration_type'] = SpellDurationType::MINUTE;
                $data['duration'] = 10;
                $data['concentration'] = true;
            } elseif ($data['duration'] == 'Концентрация вплоть до 1 минуты + 1 минута/3 уровня') {
                $data['duration_type'] = SpellDurationType::SPECIAL;
                $data['concentration'] = true;
            } elseif ($data['duration'] == 'Особая' || $data['duration'] == '1 раунд/уровень') {
                $data['duration_type'] = SpellDurationType::SPECIAL;
            } else {
                $data['duration'] = null;
            }
            if (is_null($data['duration']) && !isset($data['duration_type'])) {

                throw new \Exception('Unknown spell duration: <' . $data['duration_raw'] . '> for spell: <' . $data['name'] . '>');

            }


            $data['range_raw'] = $data['range'];
            $data['range'] = ltrim(rtrim($data['range']));
            if ($data['range'] == '60 фт.') {
                $data['range'] = 60;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == 'На себя') {
                $data['rangeType'] = SpellRangeType::SELF;
            } elseif ($data['range'] == 'Касание') {
                $data['rangeType'] = SpellRangeType::TOUCH;
            } elseif ($data['range'] == '30 фт.') {
                $data['range'] = 30;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '7 фт.') {
                $data['range'] = 7;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '300 фт.') {
                $data['range'] = 300;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '200 фт.') {
                $data['range'] = 200;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '20 фт.') {
                $data['range'] = 20;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '25 фт.') {
                $data['range'] = 25;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '30 фт.') {
                $data['range'] = 30;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '400 фт.') {
                $data['range'] = 400;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '450 фт.') {
                $data['range'] = 450;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '500 фт.') {
                $data['range'] = 500;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '150 фт.') {
                $data['range'] = 150;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '90 фт.') {
                $data['range'] = 90;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '40 фт.') {
                $data['range'] = 40;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '80 фт.') {
                $data['range'] = 80;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '16 фт.') {
                $data['range'] = 160;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '25 фт..') {
                $data['range'] = 25;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '5 фут') {
                $data['range'] = 5;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '500 фт.') {
                $data['range'] = 500;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '150 фт') {
                $data['range'] = 150;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '100 фт.') {
                $data['range'] = 100;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '10 фт.') {
                $data['range'] = 10;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == 'В пределах видимости' || $data['range'] == '1 небольшое существо или 1 кубических фут (до 10 существ или 10 кубических футов максимально)') {
                $data['rangeType'] = SpellRangeType::CAN_SEE;
            } elseif ($data['range'] == '5 фт.') {
                $data['range'] = 5;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '15 фт.') {
                $data['range'] = 15;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '1 миля') {
                $data['range'] = 1;
                $data['rangeType'] = SpellRangeType::MILLE;
            } elseif ($data['range'] == '120 фт.') {
                $data['range'] = 120;
                $data['rangeType'] = SpellRangeType::FT;
            } elseif ($data['range'] == '500 миль') {
                $data['range'] = 500;
                $data['rangeType'] = SpellRangeType::MILLE;
            } elseif ($data['range'] == 'на себя') {
                $data['rangeType'] = SpellRangeType::SELF;
            } elseif ($data['range'] == 'Неограниченная' || $data['range'] == 'Без ограничений') {
                $data['rangeType'] = SpellRangeType::UNLIMITED;
            } elseif ($data['range'] == 'На себя (5 миль радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 5;
                $data['areaSizeType'] = SpellAreaSizeType::MILLE;
                $data['areaType'] = SpellAreaType::RADIUS;
            } elseif ($data['range'] == 'На себя (15-фт. куб)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 15;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::CUBE;
            } elseif ($data['range'] == 'На себя (60-фт. конус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 60;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::CONE;
            } elseif ($data['range'] == 'На себя (100-фт. линия)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 100;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::LINE;
            } elseif ($data['range'] == 'На себя (60-фт. линия)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 60;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::LINE;
            } elseif ($data['range'] == 'На себя (15-фт. конус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 15;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::CONE;
            } elseif ($data['range'] == 'На себя (30-фт. конус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 30;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::CONE;
            } elseif ($data['range'] == '5 фт. (конус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 5;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::CONE;
            } elseif ($data['range'] == 'На себя (полусфера с радиусом 10 фт.)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 10;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::HALF_SPHERE;
            } elseif ($data['range'] == 'На себя (10-фт. радиус)' || $data['range'] == 'На себя (10 фт. радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 10;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::RADIUS;
            } elseif ($data['range'] == 'На себя (5-фт. радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 5;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::RADIUS;
            } elseif ($data['range'] == 'На себя (60 фт. радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 60;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::RADIUS;
            } elseif ($data['range'] == 'На себя (15-фт. радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaSize'] = 15;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaType'] = SpellAreaType::RADIUS;
            } elseif ($data['range'] == 'Особая') {
                $data['rangeType'] = SpellRangeType::SPECIAL;
            } elseif ($data['range'] == 'На себя (30-фт. радиус)') {
                $data['rangeType'] = SpellRangeType::SELF;
                $data['areaType'] = SpellAreaType::RADIUS;
                $data['areaSizeType'] = SpellAreaSizeType::FT;
                $data['areaSize'] = 30;
            } else {
                throw new \Exception('Unknown spell range: <' . $data['range'] . '> for spell: <' . $data['name'] . '>');
            }
            $data['name_raw'] = $data['name'];

            $data['name_ru'] = ltrim(rtrim(str_replace(['(', ')'], '', substr($data['name'], 0, strpos($data['name'], '(')))));
            $data['name_en'] = ltrim(rtrim(str_replace(['(', ')'], '', substr($data['name'], strpos($data['name'], '('), strlen($data['name'])))));

            $clearSpellDataList[$data['identifier']] = $data;
        }
        curl_close($curl);
        $this->saveSpells($clearSpellDataList, $input, $output);


//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }

    /**
     * @param array $spellDataList
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function saveSpells(array $spellDataList, InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach ($spellDataList as $spellData) {
            $spell = $this->spellRepository->findByIdentifier($spellData['identifier']);
            if (!is_object($spell)) {
                $spell = new Spell();
                $spell->setIdentifier($spellData['identifier']);
                unset($spellData['identifier']);
                $spell->setLevel($spellData['level']);
                unset($spellData['level']);
                $spell->setCastingTime($spellData['casting_time']);
                unset($spellData['casting_time']);
                $school = $this->spellSchoolRepository->findByIdentifier($spellData['school']);
                if (!is_object($school)) {
                    throw new \Exception('unknown school: ' . $spellData['school']);
                }
                $spell->setSchool($school);
                unset($spellData['school']);
                unset($spellData['school_raw']);
                unset($spellData['schoolEn']);
                if ($spellData['rangeType'] == SpellRangeType::SELF || $spellData['rangeType'] == SpellRangeType::TOUCH || $spellData['rangeType'] == SpellRangeType::SPECIAL || $spellData['rangeType'] == SpellRangeType::CAN_SEE || $spellData['rangeType'] == SpellRangeType::UNLIMITED) {
                    if (is_string($spellData['range'])) {
                        $spellData['range'] = null;
                    }
                }
                if (is_string($spellData['range'])) {
                    throw new \Exception('wrong range: ' . $spellData['range']);
                }
                $spell->setRangeDistance($spellData['range']);
                unset($spellData['range']);
                $spell->setDescriptionRu($spellData['description']);
                unset($spellData['description']);
                $verbal = false;
                $somatic = false;
                $materialComponents = [];
                foreach (explode(',', $spellData['components']) as $component) {
                    if (rtrim(ltrim($component)) == 'В') {
                        $verbal = true;
                    } elseif (rtrim(ltrim($component)) == 'С') {
                        $somatic = true;
                    } elseif (rtrim(ltrim($component)) == 'М') {

                    } else {
                        $materialComponents[] = rtrim(ltrim($component));
                    }

                }
                if (!empty($materialComponents)) {
                    $spell->setMaterialComponents($materialComponents);
                } else {
                    $spell->setMaterialComponents(null);
                }
                $spell->setVerbalComponent($verbal);
                $spell->setSomaticComponent($somatic);
                unset($spellData['components']);
                if ($spellData['duration_type'] == SpellDurationType::UNTIL_DISSIPATES || $spellData['duration_type'] == SpellDurationType::SPECIAL) {
                    if (is_string($spellData['duration'])) {
                        $spellData['duration'] = null;
                    }
                }
                if (is_string($spellData['duration'])) {
                    throw new \Exception('wrong duration: ' . $spellData['duration']);

                }
                $spell->setDuration($spellData['duration']);
                unset($spellData['duration']);
                $spell->setDurationType($spellData['duration_type']);
                unset($spellData['duration_type']);
                foreach ($spellData['classes'] as $classId) {
                    $class = $this->characterClassRepository->findByIdentifier($classId);
                    if (!is_object($class)) {
                        throw new \Exception('unknown class: ' . $classId);
                    }
                    $spell->addCharacterClass($class);
                }
                unset($spellData['classes']);
                unset($spellData['classes_raw']);
                foreach ($spellData['source'] as $sourceName) {
                    $source = $this->sourceRepository->findByName($sourceName);
                    if (!is_object($source)) {
                        throw new \Exception('unknown source: ' . $sourceName);
                    }
                    $spell->addSource($source);
                }
                unset($spellData['source']);
                unset($spellData['raw_source']);
                unset($spellData['name']);
                $spell->setCastingTimeDescriptionRu($spellData['casting_time_description_ru']);
                unset($spellData['casting_time_description_ru']);
                $spell->setIsRitual($spellData['is_ritual']);
                unset($spellData['is_ritual']);
                unset($spellData['raw_casting_time']);
                $spell->setCastingTimeType($spellData['casting_time_type']);
                unset($spellData['casting_time_type']);
                $spell->setConcentration($spellData['concentration']);
                unset($spellData['concentration']);
                unset($spellData['duration_raw']);
                unset($spellData['range_raw']);
                $spell->setRangeType($spellData['rangeType']);
                unset($spellData['rangeType']);
                unset($spellData['name_raw']);
                $spell->setNameRu($spellData['name_ru']);
                unset($spellData['name_ru']);
                $spell->setNameEn($spellData['name_en']);
                unset($spellData['name_en']);
                if (isset($spellData['areaType'])) {
                    $spell->setAreaType($spellData['areaType']);
                    unset($spellData['areaType']);
                } else {
                    $spell->setAreaType(null);
                }

                if (isset($spellData['areaSizeType'])) {
                    $spell->setAreaSizeType($spellData['areaSizeType']);
                    unset($spellData['areaSizeType']);
                } else {
                    $spell->setAreaSizeType(null);
                }

                if (isset($spellData['areaSize'])) {
                    $spell->setAreaSize($spellData['areaSize']);
                    unset($spellData['areaSize']);
                } else {
                    $spell->setAreaSize(null);
                }


                if (!empty($spellData)) {
                    print_r($spellData);
                    die();
                }
                $em->persist($spell);

            }
        }
        $em->flush();
    }
}
