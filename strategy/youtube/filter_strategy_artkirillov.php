<?php

/**
* Шаблон проектирования "Стратегия".
* От Артёма Кирилова: https://www.youtube.com/watch?v=eXT-yR1eCCY
* 
* Демонстрируется использование композиции вместо наследования.
* Композиция - это связь типа "имеет", а наследование - это связь типа "является".
* Условие: отсутствует преимущество переиспользования кода через наследование, 
* т.е. в наследуемых классах нам каждый раз приходится полностью переопределять поведение методов.
* Задача: описать набор фильтров для обработки изображений.
* Наследование не подходит, так как у каждого фильтра имеется свое уникальное поведение 
* и нам пришлось бы каждый раз переопределять методы базового класса.
* Взглянем на проблему с другой стороны: 
* будем рассматривать функцию фильтрации как некое поведение объекта.

* Шаблон не стоит применять, если алгоритм постоянный и не содержит вариаций.
**/

final class TestImage
{
	
}

// Шаг 1. Создадим интерфейс, в котором будет метод по обработке изображений.
interface FilterStrategy
{
	public function process(TestImage $image):TestImage;
}

// Шаг 2. Создадим базовый класс "Фильтр" 
// со свойством отвечающим за конкретное поведение фильтрации.
// В свойство можно присвоить только объекты созданные по нашему интерфейсу.
// У объекта переданного в свойство будем запускать процесс применения фильтра.

final class Filter 
{
	public ?FilterStrategy $filterStrategy;
	
	public function applyFilter(TestImage $image)
	{
		if (is_null($this->filterStrategy)) return;
		
		$this->filterStrategy->process($image);
	}
}

// Шаг 3. Создадим различные стратегии поведения, реализуя наш интерфейс
final class SepiaFilter implements FilterStrategy
{
	public function process(TestImage $image):TestImage
	{
		echo "Apply SEPIA filter to image\n";
		return $image;
	}
}
final class BWFilter implements FilterStrategy
{
	public function process(TestImage $image):TestImage
	{
		echo "Apply B&W filter to image\n";
		return $image;
	}
}
final class DistorionFilter implements FilterStrategy
{
	public function process(TestImage $image):TestImage
	{
		echo "Apply DISTORION filter to image\n";
		return $image;
	}
}
// Шаг 4. Применяем реализованный шаблон, динамически меняя стратегию поведения объекта.
$testImage = new TestImage();
$filter = new Filter();

$filter->filterStrategy = new SepiaFilter();
$filter->applyFilter($testImage);

$filter->filterStrategy = new BWFilter();
$filter->applyFilter($testImage);

$filter->filterStrategy = new DistorionFilter();
$filter->applyFilter($testImage);

/*
Вывод консоли:

Apply SEPIA filter to image
Apply B&W filter to image
Apply DISTORION filter to image
*/