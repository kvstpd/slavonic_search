# Church Slavonic Tools / Инструментарий для церковнославянских текстов 

<i>(Description in Russian is <a href="#russian">here</a> / Описание на русском <a href="#russian">здесь</a>)</i>

Web tools for searching, studying and encoding Church Slavonic texts.

The aim of this project is to create a set of tools that perform word search and analysis of Church Slavonic books, help one to learn and explore Church Slavonic words and convert between existing encodings of Church Slavonic (Unicode, legacy UCS and possibly, in the future, HIP).

Right now there are  several "components" of these tools:

<b>Database builder</b>: Creates index & dictionary database from a fixed library of Slavonic texts (Bible, lithurgical books, Philokalia). Presently can make two kinds of databases: one for Search tool and another (simpler & more compact) for Slavoniser app (link to project will be added soon).

<b>Search tool</b>: Performs single word search over these texts (or a subset of them), finding number of occurences, their locations in the books and showing context of each occurence. Search queries can be entered in Unicode, legacy UCS encoding and simplified Civil Cyrillic (perhaps the most useful option that helps to find all variants of accents, wide and narrow letters, titlo positions, etc.). "Begins with", "Ends with", "Contains" and "Exact match" queries are possible right now.

<b>Church Slavonic Trainer</b>: Displays Slavonic words of certain length and complexity (chosen by selecting "Difficulty level") in random batches, to help user to remember them, read them properly and perhaps discover some words one has never met before.

<b>Slavonic Encoding Converter</b>: Converts user specified text between different encodings (presently Unicode and UCS).

PHP is used as the main programming language (mbstring extension is required) and SQLite file as database backend (MySQL may also work but right now is much slower and almost untested).

<b>Installation</b> is presently done by copying all the project files to a location on Web server and making sure PHP scripts have write permission to create & write to SQLite database file in the same directory. 

<i>That's not exactly secure so it's RECOMMENDED to edit <b>config.php</b> file to specify a more fitting path for database file (in DB_NAME constant) - that PHP still needs permissions to write to.</i>

After all files are in place one can populate the dictionary database by visiting "Populate DB" link of script's main page top menu.

It may take more time & server resources to build the database than web server settings allow. In that case one can instead login to server shell and run

```sh
php builder.php --populate
```

in the directory containing project files -- assuming shell access and php command are available. Other options, including downloading a pre-built database, will be added soon.

<i>Another HIGHLY RECOMMENDED action is to set ADMIN_MODE constant to 0 in <b>config.php</b> file as soon as the database is built. Later, if you modify any library files in "books" subdirectory, you can set ADMIN_MODE back to 1, delete the old database file, re-build it and once again set ADMIN_MODE to 0. This can prevent accidental database corruption and make script usage more secure.</i>

Still need to be implemented: 
<ol>
<li>Beautiful UI, </li>
<li>easy-to-navigate front page, </li>
<li>multi-word search (with word order and distance), </li>
<li>indexing and searching user-specified texts,</li>
<li>extending the library used by the tools to contain more books, like Pre-XVII century reform ones, modern XX-XXI cent. texts, Church Fathers writings),</li>
<li>adding Tables of Contents to most of the books to make search show more human-readable word locations,</li>
<li>Trainer needs more modes like "learn to read words with 6 consonants in a row",</li>
<li>Encoding Converter must also produce Civil Cyrillic text with accents.</li>
</ol>

Church Slavonic texts are taken from Ponomar project Web page, https://www.ponomar.net (and slightly modified)
Fonts are taken from https://www.ponomar.net and http://irmologion.ru 


# <a name="russian" id="russian"> Инструментарий для церковнославянских текстов

Веб-инструментарий для поиска, изучения и смены кодировки текстов на церковнославянском языке.

Цель данного проекта - создать инструментарий для поиска по текстам книг на церковнославянском языке, их анализа, помощи в освоении сложных слов и преобразования между разными кодировками церковнославянского текста (современный Unicode, более старый UCS, и, возможно, в дальнейшем, HIP).

На сегодня инструментарий состоит из следующих компонентов:

<b>Построитель базы данных</b>: Индексирует тексты и создаёт словарь на основе заданной библиотеки церковнославянских книг (Елизаветинская Библия, богослужебные книги, Добротолюбие). В данный момент возможно создание двух баз данных: одна для поиска по книгам, вторая для приложения "Славянизатор" (проект и ссылка на него появятся в ближайшем будущем).

<b>Поиск</b>: Ищет слово в текстах книг библиотеки (всех или нескольких выбранных), сообщает, сколько раз оно встречается, отображает найденные места с контекстом употребления слова. Запросы для поиска могут быть в кодировке Unicode, UCS, или на упрощённой гражданской кириллице. Последний вариант наиболее полезен для нахождения различных вариантов ударений, узкого и широкого написания букв, расположения титла и т.д. На данный момент возможен поиск по началу, окончанию или середине слова, а также по точному значению. 

<b>Тренажёр чтеца на церковнославянском</b>: Отображает слова заданной длины (определяется выбором уровня сложности) в виде табличек по 10, в случайном порядке, для помощи в запоминании и правильном прочтении сложных слов. Возможно, пользователь обнаружит и незнакомые ему слова.

<b>Перекодировщик</b>: Меняет кодировку текста, введённого пользователем (на текущий момент - из Unicode в UCS и наоборот).

В качестве основного языка программирования используется PHP (обязательно наличие модуля mbstring), базой данных служит файл SQLite (БД MySQL также может работать, но пока что работа с ней недостаточно оптимизирована и протестирована).

<b>Установка</b> на текущий момент осуществляется копированием всех файлов проекта на веб-сервер. Необходимо убедиться, что скрипты PHP имеют права доступа, чтобы создавать и записывать файл базы данных в той директории, где они находятся.  

<i>Поскольку такой подход может оказаться недостаточно безопасным, РЕКОМЕНДУЕТСЯ отредактировать файл <b>config.php</b> и указать другой, более подходящий путь для файла БД (задаётся в константе DB_NAME). Естественно, PHP должен иметь права доступа к данному пути.</i>

После копирования файлов необходимо создать базу данных, нажав ссылку "Populate DB" в верхнем меню основной страницы скрипта. 

Создание и наполнение базы данных может занять существенно больше времени и ресурсов сервера, чем его настройки позволяют. Если есть такие ограничения, можно зайти в командную оболочку сервера, сменить текущую директорию на ту, где находятся файлы проекта, и выполнить команду:

```sh
php builder.php --populate
```

(Конечно, если есть доступ к командой оболочке сервера и команде php). Другие варианты создания БД и ссылка на готовый файл будут добавлены позже.

<i>Также ВЕСЬМА РЕКОМЕНДУЕТСЯ установить константу ADMIN_MODE в файле <b>config.php</b> равной 0 как только создание и наполнение базы данных завершатся. В дальнейшем, если вы меняете содержание файлов в субдиректории "books" и требуется их индексировать заново, можно снова установить ADMIN_MODE равным 1, пересоздать файл базы данных, а затем снова вернуть значение 0. Это поможет избежать повреждения базы данных и повысит безопасность работы инструментария.</i>

Всё ещё не реализованы - но, надеемся, будут:

<ol>
<li>Более привлекательный внешний вид страниц, </li>
<li>простая и понятная навигация по инструментам, </li>
<li>поиск нескольких слов (с указанием порядка и расстояния между ними), </li>
<li>индексация и поиск по книгам, добавленным в библиотеку пользователем,</li>
<li>расширение библиотеки церковнославянских текстов, (напр. книги в дониконовской орфографии, новые богослужебные тексты XX-XXI вв., святоотеческие творения), </li>
<li>добавление подробного содержания к большинству книг для более ясного отображения результатов поиска,</li>
<li>новые режимы Тренажёра чтеца, например, "чтение слов, содержащих 6 согласных подряд",</li>
<li>Перекодировщик должен уметь конвертировать тексты в гражданскую кириллицу с ударениями.</li>
</ol>

Книги взяты с веб-страницы Ponomar project, https://www.ponomar.net (и немного отредактированы)
Церковнославянские шрифты взяты с сайтов https://www.ponomar.net и http://irmologion.ru 
