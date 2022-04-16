# Генерация случайного числа

> Генерация случайного числа с ID и кэшем

## Содержание

- [Подготовка](#подготовка)
- [Мысли](#мысли)
- [Расчет времени](#расчет-времени)

## Подготовка

```shell
git clone ... && cd ... && composer install

php -S localhost:8000
```

Библиотек нет. Используется автоподгрузка классов по `PSR-4`.

# Мысли

Генерация `id`. Тк не используем БД или что-то подобное где генерируется `id`, то придумывать надо что-то свое.
Порядковое целочисленное `id` надо где-то хранить последний `id`. В ТЗ используем только файлы, но при большом
количестве запросов будет на последнем `id` сгенерированно несколько случайных чисел, но записанно будет последнее.
Подробности можно обсудить, но думаю понятно. В ТЗ строго не было указания типа `id`. Поэтому генерирую строковый:

```php
 $id = sha1(microtime(true) . '_' . rand(1000, 1e6));
 ```

Сначала думал «схитрить» и сделать попроще через [PSR-16](https://www.php-fig.org/psr/psr-16/), но потом переделал
на [PSR-6](https://www.php-fig.org/psr/psr-6/).

По поводу АПИ. Вроде как предполагаются маршруты и тп, но можно опять-таки «схитрить» и сделать просто
директорию `generate` и не пытаться написать роутер. Думал, думал и в итоге накидал что-то простое. Не знаю чтоило ли
упарываться. Для примера есть директория `gen`. Это как можно было бы не мучаться с
роутером. `http://localhost:8000/gen/` это тоже самое что и `http://localhost:8000/generate/` только без роутера.

## Расчет времени

| Работа | Время, в мин | Примечание |
|---|---|---|
| Обдумать | 30 мин | |
| Router / Response / Controllers | 60 мин | По PSR не стал делать. Много будет работы. Обычно использую готовые решения. Решил что по PSR требуется сама генерация. |
| Изучение PSR-6 и PSR-16 | 30 мин | Так работал всегда с готовыми библиотеками. Знаком с данными интерфесами, но не задумывался что там и как внутри |
| RandCache | 70 мин | Набросок, потом причесывание. |
| Написание документации | 45 мин | | 
| Итого: | 235 мин | |