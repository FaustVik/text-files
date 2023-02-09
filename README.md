# Text file

## Описание

`text-files` — это мощная библиотека для работы с текстовыми файлами и CSV в PHP. Она предоставляет набор инструментов для управления директориями, файлами, чтения и записи данных, а также обработки CSV файлов. Библиотека включает удобные классы и методы для выполнения различных операций с файловой системой.

## Основные возможности

### Управление директориями
- Создание и удаление вложенных директорий.
- Сканирование директорий и получение информации о файлах.
- Проверка существования, доступности для чтения и записи файлов.

### Работа с файлами
- Получение информации о файлах (имя, размер, расширение).
- Проверка доступности для чтения и записи.
- Блокировка и разблокировка файлов для безопасной многопоточной работы.

### Чтение и запись текстовых файлов
- Чтение файлов в массив строк или одну строку.
- Запись данных в файл (перезапись или добавление).
- Пропуск пустых строк при чтении.
- Преобразование массива данных в JSON перед записью.

### Обработка CSV файлов
- Чтение и запись CSV файлов.
- Удаление столбцов и строк.
- Обновление заголовков.
- Поддержка различных кодировок и настроек CSV (разделители, экранирование и т.д.).

## Установка

Для установки библиотеки используйте Composer:

```bash
composer require faustvik/text-files
```

## Использование

Работа с директориями

Создание вложенных директорий:

```php
use FaustVik\Files\Helpers\Directory\DirectoryOperation;
$directoryPath = '/path/to/nested/directory';
DirectoryOperation::creatingNestedDirectories($directoryPath);
```

Удаление директории со всеми вложенными файлами и директориями:  
php
```php
use FaustVik\Files\Helpers\Directory\DirectoryOperation;
$directoryPath = '/path/to/nested/directory';
DirectoryOperation::deleteDir($directoryPath);
```

Получение содержимого директории:  
```php
use FaustVik\Files\Helpers\Directory\DirectoryInfo;
use FaustVik\Files\Helpers\Directory\EnumSortDirectory;
$directoryPath = '/path/to/directory';
$contents = DirectoryInfo::scan($directoryPath, EnumSortDirectory::ASC); // Сортировка по возрастанию
```

Работа с файлами

Получение информации о файле:  
```php
use FaustVik\Files\Helpers\File\FileInfo;
$filePath = '/path/to/file.txt';
$name = FileInfo::getName($filePath); // Получение имени файла
$size = FileInfo::getSize($filePath); // Получение размера файла в байтах
$extension = FileInfo::getExtension($filePath); // Получение расширения файла
$isReadable = FileInfo::isReadable($filePath); // Проверка доступности для чтения
$isWritable = FileInfo::isWritable($filePath); // Проверка доступности для записи
```

Чтение и запись текстовых файлов

Чтение файла в массив строк:

```php
use FaustVik\Files\Text\TextFileManager;
use FaustVik\Files\Contracts\File\FileContract;
use FaustVik\Files\Contracts\Text\TextSettingReaderContract;
$file = new YourFileImplementation('path/to/file.txt');
$settings = new YourSettingsImplementation();
$reader = new TextFileManager($file, $settings);
$arrayContent = $reader->readToArray();
```

Запись данных в файл:  
```php
$success = $reader->overWrite("new content"); // Перезапись содержимого файла
$success = $reader->write("appended content"); // Добавление данных в конец файла
$success = $reader->appendToStartFile("prepended content"); // Добавление данных в начало файла
```

Обработка CSV файлов

Чтение CSV файла:

```php
use FaustVik\Files\Csv\CsvManager;
use FaustVik\Files\Contracts\Csv\CsvSettingReaderContract;
use FaustVik\Files\Contracts\File\CsvFileContract;
$csvSettings = new CsvSettings();
$csvFile = new CsvFile('/path/to/your/file.csv');
$csvReader = new CsvManager($csvFile, $csvSettings);
$data = $csvReader->read();
print_r($data);
```

Запись в CSV файл:  
```php
$fields = [
['id' => 1, 'name' => 'John', 'age' => 30],
['id' => 2, 'name' => 'Jane', 'age' => 25]
];
$csvReader->write($fields);
```

Настройки CSV:  
```php
$csvSettings = new CsvSettings();
$csvSettings->setSeparator(';');
$csvSettings->setEncoding('UTF-8');
$csvSettings->setEscapeChar('\\');
$csvSettings->setEnclosureChar('"');
```

Исключения

Библиотека включает пользовательские исключения для обработки ошибок:

    DirectoryException: выбрасывается при ошибках, связанных с директориями.
    FileNotFoundException: выбрасывается при попытке получить информацию о несуществующем файле.
    IsNotResource: выбрасывается при передаче некорректного ресурса (например, не открытого файла).
     
