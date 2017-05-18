# mysql-hash-index
PHP package that implements Hash indexes for mysql. Maybe U'll need separate workerman process. Maybe will be converted to mysql's plugin.

В последнее время на многих проектах наблюдаю самый критичный функционал - это индексация. Причем, в первую очередь - неспособность проставить индексы, а уже во вторую - несовершенство механизмов индексации. Доходит до абсурда - люди скейляться в кластера с репликацией по binary level вместо того, чтобы проставить индексы и изюавиться от проблемы n+1. Вы слышали о тпкой проблеме? Я - нет. До последнего времени. 
Проблема: катастрофически не хватает знаний об индексах именно у php разрабов.
Проблема: катастрофически херовы возможности индексации у mysql. Да плюс давно было желание написать SELECT FOR LOCK ... SKIP LOCKED.
В общем, с целью сделать мир чуть менее тормознутым, я принес вам набор классов и интерфейсов, написанный на PHP7.1, характеризующий все возможные варианты индексации. Пишите на нем все, что угодно. Ща для примера мы запилим демона, реализующего пригодные для джоина неуникальные хеш-индексы.

Обязательно напилю интерфейс для датастораджа. Это SOLLID'но. Но до абсурда дробить интерфейсы не стоит.

Зачем для джоина двух таблиц два индекса? Один с двумя референсами на id. Общий. На PHP. Вы представляете скорость доступа? А можно на mongo. там связям даже id не будут нужны. И неблокирующий доступ обязательно.

Composer's plugin for launchpad.

Самый простой способ понять что такое индексы - попробовать написать индексы. У MySQL вроде не было хеш-индексов. А мне сейчас как раз придется, возможно, разджоинивать массу запросов. Так что встречайте - написанные на php хеш-индексы. Возможно, не идеальные, но работают. Допилю, устрою им нагрузочное тестирование - и суну на боевое крещение. Пригодные для использования с любыми датасторами, расширяемые с документированным фиксированным api.

Основная цель - написать тулзу, пригодную для разджоинивания запросов.

Reflection можно ролностью охарактеризовать 2 индексами. Я могу на php написать проверялку индексов в базе мускуля заодно. Прямо в acl9-relation, например. Идеально простое описание структуры связей в бд. 

А еще я этот функционал могу использовать для OnyxBook2.0. Надо будет только научиться дампить данные в бекап, например yaml-encoded file. Или написать для него свой стораж engine? С фиксированным размером строки, неблокирующимся доступом. В редисе ему хранилище надо будет написать. И в index_name.json. И workerman. И reverse index, с btree из stdlib. Phar-файл. Посмотрим, насколько php7.1 действительно является языком общего назначения.

Хранилища обязательно должны быть инкапсулированы на уровне интерфейсов. Я должен иметь возможность сохранить индекс от двух таблиц mysql. 

Plugin API должен быть на JS. Первый плагин - функция, вычисляющяя индексируемое значение. Входной параметр - значение текущей строки и n ее соседок. То есть получающая данные группами по n строк. Дешевая организация постраничной индексации. Ограничения на n отдаются под управлением реализации backend'a.

А еще можно держать в памяти сджоенную таблицу - id соответствующих строк с каждой стороны. Да все что угодно!

А еще - установку из композера и совместимость с доктриновым query builder. И пакет для launchpad. И статью на хабре. B плагин к композеру для launchpad если еще нет такого. 

Если получится - мир станет чуть меньше тормозить. Будущее наступило - мы пишем индексы к mysql на php. Шутка про драйвера на JS уже не такая смешная, правда?

##Способ запуска
https://github.com/shaneharter/PHP-Daemon - EventLoop. Я плакал. Как в иксах и как в дельфях. Дайте две. Параллелизация в две строчки. Я перестпл плакать и срочно вспомнил что нужно еще нагрузочное тестировать. А бить нужно именно по join - они всегда слабое место. А бить нужно асинхронно и полностью контролируемо, а не как Locust. 

##Способ вывода
Можно совместить c PHPUnit - получится прикольная нагрузочная тестилка. PHPUnit API перепроверить и на предмет потокобезопасности кода тоже. Ебать будущее.

##Способ проверки
Нагрузочное тестирование - два процесса конкурируют за ресурсы и логгируют производительность своей работы.

##Fault tolerance
По-потоковое выполнение неперепроверенного кода и выбранный способ позволяют, при желании, обеспечить безопасность до уровня Erlang/OTP

##JS engine
http://php.net/manual/ru/v8js.examples.php

##Выбор технологий
Почему php, js и mysql? Да я просто их люблю. 

##Название статьи
 - А в чем вы данные храните? 
 - В памяти. Их сильно много.
Моя собственная монга с reverse index на php. Если получится обеспечить достаточное быстродействие и fault tolerance - можно кешировать критичные данные прямо в php-процессах. Универсализировать структуру не получится - да и не надо. Язык-то - php. Хай наследуют классы друг друга и создают интерфейсы. Может даже события генерят. Полная асинхронщина. Можно движок для онлайн игры написать.

##Движение дальше
Console using PHP, JS and Ruby (using opal probably).


