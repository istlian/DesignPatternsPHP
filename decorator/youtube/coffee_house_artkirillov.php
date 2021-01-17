<?php

/**
* Шаблон проектирования "Декоратор".
* От Артёма Кирилова: https://www.youtube.com/watch?v=4sRYRw-ySRQ
* 
* Демонстрируется использование композиции вместо наследования.
* Данный шаблон можно применять на любой кодовой базе, которая была написана до вас.
* Условие: необходимо на основе чего-то одного создавать много различных вариантов.
* Задача: имеется кофейня, где готовят различные виды кофе. 
* Как правило все виды кофе создаются на базе Эспрессо:
* 1. Американо - это эспрессо с горячей водой.
* 2. Капучино - это эспрессо с молоком и со взбитым молоком.
* 3. Гляссе - это эспрессо с мороженным и тертым шоколадом.
* 4. Мокко - эспрессо со взбитыми сливками и шоколадом. 
* В добавок к этому, любой напиток может быть модифицирован добавкой, например сиропом.
* 
* Плохие варианты решения:
* 1. Создать отдельные классы для каждого вида продукта. Плохо: очень много классов.
* 2. Заложить логику списком IF-ELSE. Плохо: сложно и нужно будет постоянно править класс.
* Хороший вариант решения - это использовать шаблон проектирования "Декоратор".
**/

// Шаг 1. Создадим интерфейс. В нем будут 2 метода: возвращение стоимости напитка и возвращение
// ингредиентов напитка.
interface Coffee 
{
	public function cost():float;
	public function ingredients():string;
}

// Шаг 2. Опишим наш базовый класс основного напитка, который будет реализовывать интерфейс
final class Espresso implements Coffee 
{
	
	public function cost():float
	{
		return 100.00;
	}
	
	public function ingredients():string
	{
		return "Espresso";
	}
	
}

// Шаг 3. Опишем класс декоратора, который будет реализовывать все наши варианты добавок.
// Важный момент, он также должен реализовывать наш интерфейс.
class CoffeeDecorator implements Coffee
{
	// Шаг 3.1. Объявим приватную переменную, в которой будем хранить объект базового напитка.
	private $coffee;
	
	// Шаг 3.2. Опишем конструктор, через который в класс будем передавать базовый напиток 
	// типа Coffee
	public function __construct(Coffee $coffee)
	{
		$this->coffee = $coffee;
	}
	
	// Шаг 3.3. Реализуем методы из указанного нами интерфейса
	
	public function cost():float
	{
		return $this->coffee->cost();
	}
	
	public function ingredients():string
	{
		return $this->coffee->ingredients();
	}
}

// Шаг 4. Создаем нужное количество различных добавок.
// Для этого создаем варианты классов наследованием от нашего суперкласса CoffeeDecorator

class Milk extends CoffeeDecorator
{
	// 4.1. В каждом варианте переопределяем наследованные методы, подставляя свои данные.
	public function cost():float
	{
		// 4.2. Из нашего суперкласса получаем данные из базового класса (напитка) и добавляем 
		// к ним свои данные, видоизменяя их.
		return parent::cost() + 20.00;
	}
	
	public function ingredients():string
	{
		return parent::ingredients() . ", Milk";
	}
}

class Whip extends CoffeeDecorator
{
	public function cost():float
	{
		return parent::cost() + 30.00;
	}
	
	public function ingredients():string
	{
		return parent::ingredients() . ", Whip";
	}
}

class Chocolate extends CoffeeDecorator
{
	public function cost():float
	{
		return parent::cost() + 50.00;
	}
	
	public function ingredients():string
	{
		return parent::ingredients() . ", Chocolate";
	}
}

// Шаг 5. Создайте экземпляр базового класса
$espresso = new Espresso();
// Шаг 6. Создайте из базового класса нужный напиток, например Капучино
$cappuccino = new Whip(new Milk($espresso));
// Шаг 6.1. Вы также можете передать уже задекорированный объект, чтобы на его основе добавить
// новый ингредиент.
$cappuccinoWithChocolate = new Chocolate($cappuccino);

// Шаг 7. Используйте полученные задекорированные объекты.
echo $espresso->ingredients(), "\n", $espresso->cost(), "\n";
echo $cappuccino->ingredients(), "\n", $cappuccino->cost(), "\n";
echo $cappuccinoWithChocolate->ingredients(), "\n", $cappuccinoWithChocolate->cost(), "\n";

/*
Результат запуска скрипта:

Espresso
100
Espresso, Milk, Whip
150
Espresso, Milk, Whip, Chocolate
200
*/