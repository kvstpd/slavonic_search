# Church Slavonic Tools

Web tools for searching, studying and encoding Church Slavonic texts.

The aim of this project is to create a set of tools that perform word search and analysis of Church Slavonic books, help one to learn and explore Church Slavonic words and convert between existing encodings of Church Slavonic (Unicode, legacy UCS and possibly, in the future, HIP).

Right now there are functions that create a dictionary database from fixed library of Slavonic texts (Bible, lithurgical books, Philokalia), and then can do single word search over these texts (or a subset of them), finding number of occurences, their locations in the books and showing context of each occurence.

Search queries can be entered in Unicode, lecacy UCS encoding and simplified Civil Cyrillic (perhaps the most useful option that helps to find all variants of accents, wide and narrow letters, titlo positions, etc.). "Begins with", "Ends with", "Contains" and "Exact match" queries are possible right now.

Also there is "Church Slavonic Trainer" script that displays Slavonic words of certain length (chosen by selecting "Difficulty level") in batches, to help user to remember them, read them properly and perhaps discover some words one has never met before.

Finally, tools contain Slavonic Encoding Converter that converts user specified text between different encodings (presently Unicode and UCS).

PHP is used as the main programming language (mbstring extension is required) and SQLite as database backend (MySQL may also work but right now is much slower and almost untested).

Still need to be implemented: 
<ol>
<li>Beautiful UI, </li>
<li>easy-to-navigate front page, </li>
<li>multi-word search (with word order and distance), </li>
<li>indexing and searching user-specified texts (also extending the library used by the tools to contain more books, like Pre-XVII century reform ones, modern XX-XXI cent. texts, Church Fathers writings). </li>
<li>Trainer needs more modes like "learn to read words with 6 consonants in a row". </li>
<li>Encoding Converter must also produce Civil Cyrillic text with accents.</li>
</ol>

Church Slavonic texts are taken from Ponomar project Web page, https://www.ponomar.net (and slightly modified)
Fonts are taken from https://www.ponomar.net and http://irmologion.ru 


# Инструментарий для церковнославянских текстов

Веб-инструментарий для поиска, изучения и смены кодировки текстов на церковнославянском языке.

Более полное описание на русском будет написано вскоре.
