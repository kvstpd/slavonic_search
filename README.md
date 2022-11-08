# Church Slavonic Tools (русское описание <a href="#russian">здесь</a>)

Web tools for searching, studying and encoding Church Slavonic texts.

The aim of this project is to create a set of tools that perform word search and analysis of Church Slavonic books, help one to learn and explore Church Slavonic words and convert between existing encodings of Church Slavonic (Unicode, legacy UCS and possibly, in the future, HIP).

Right now there are  several "components" of these tools (not all of them yet separated in the code):

<b>Database builder</b>: Creates index & dictionary database from a fixed library of Slavonic texts (Bible, lithurgical books, Philokalia). Presently can make two kinds of databases: one for Search tool and another (simpler & more compact) for Slavoniser app (link to project will be added soon).

<b>Search tool</b>: Performs single word search over these texts (or a subset of them), finding number of occurences, their locations in the books and showing context of each occurence. Search queries can be entered in Unicode, legacy UCS encoding and simplified Civil Cyrillic (perhaps the most useful option that helps to find all variants of accents, wide and narrow letters, titlo positions, etc.). "Begins with", "Ends with", "Contains" and "Exact match" queries are possible right now.

<b>Church Slavonic Trainer</b>: Displays Slavonic words of certain length and complexity (chosen by selecting "Difficulty level") in random batches, to help user to remember them, read them properly and perhaps discover some words one has never met before.

<b>Slavonic Encoding Converter</b>: Converts user specified text between different encodings (presently Unicode and UCS).

PHP is used as the main programming language (mbstring extension is required) and SQLite file as database backend (MySQL may also work but right now is much slower and almost untested).

<b>Installation</b> is presently done by copying all the project files to a location on Web server and making sure PHP scripts have write permission to create & write to SQLite database file in the same directory. 

<i>That's not exactly secure so it's RECOMMENDED to edit <b>config.php</b> file to specify a more fitting path for database file - that PHP still needs permissions to write to.</i>

After all files are in place one can populate the dictionary database by visiting <b>[address_to_server_location]/index.php?populate</b>

It may take more time & server resources to build the database than web server settings allow. In that case one can instead login to server shell and run <b>php-cgi index.php populate</b> in the directory containing project files -- assuming shell access and php-cgi are available. Other options, including downloading a pre-built database, will be added soon.

<i>Another HIGHLY RECOMMENDED action is to set ADMIN_MODE to 0 in <b>config.php</b> file as soon as the database is built. Later, if you modify any library files in "books" subdirectory, you can set ADMIN_MODE back to 1, delete the old database file, re-build it and once again set ADMIN_MODE to 0. This can prevent accidental database corruption and make script usage more secure.</i>

Still need to be implemented: 
<ol>
<li>Beautiful UI, </li>
<li>easy-to-navigate front page, </li>
<li>multi-word search (with word order and distance), </li>
<li>indexing and searching user-specified texts,</li>
<li>extending the library used by the tools to contain more books, like Pre-XVII century reform ones, modern XX-XXI cent. texts, Church Fathers writings). </li>
<li>adding Tables of Contents to most of the books to make search show more human-readable word locations</li>
<li>Trainer needs more modes like "learn to read words with 6 consonants in a row".</li>
<li>Encoding Converter must also produce Civil Cyrillic text with accents.</li>
</ol>

Church Slavonic texts are taken from Ponomar project Web page, https://www.ponomar.net (and slightly modified)
Fonts are taken from https://www.ponomar.net and http://irmologion.ru 


# <a name="russian"> Инструментарий для церковнославянских текстов

Веб-инструментарий для поиска, изучения и смены кодировки текстов на церковнославянском языке.

Более полное описание на русском будет написано вскоре.
