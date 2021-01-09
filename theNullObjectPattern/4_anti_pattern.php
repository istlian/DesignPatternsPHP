<?php

declare(strict_types = 1);

namespace Istlian\DesignPatterns\NullObject;

// 1. Объявляем интерфейс
// Он необходим для реализации паттерна, так как по нему должны
// быть реализованы 2 разных по содержанию объекта, но с одинаковым
// интерфейсом доступа к данным. 
interface IUserTaxes
{
	public function getUserTaxes();
	// 1.1. Дополнительный метод проверяющий на null
	// Это признак анти-паттерна.
	public function isNull():bool;
}
// 2. Реализуем интерфейс в основном классе
class UserTaxes implements IUserTaxes
{
	protected $account;
	protected $income_tax;
	protected $property_tax;
	
	public function __construct(string $account, int $income_tax, int $property_tax)
	{
		$this->account = $account;
		$this->income_tax = $income_tax;
		$this->property_tax = $property_tax;
	}
	
	public function getUserTaxes() 
	{
		return (object) [
			'account' => $this->account, 
			'income_tax' => $this->income_tax, 
			'property_tax' => $this->property_tax
		];
	}
	
	public function isNull():bool
	{
		return false;
	}	
}
// 3. В дополнение к основному реализуем ничего не делающий класс
class NullUserTaxes implements IUserTaxes
{	
	public function getUserTaxes() 
	{
		return (object) [
			'account' => 'Unknown',
			'income_tax' => 0,
			'property_tax' => 0
		];
	}
	public function isNull():bool
	{
		return true;
	}
}


// Репозиторий с объектами пользователей
//
// Вспомогательный класс, который по запросу выдает нам либо объект UserTaxes
// с существующими данными либо объект NullUserTaxes с данными по умолчанию. 
class TaxesRepository
{
	private $userTaxes = [];

	public function __construct()
	{
		$this->userTaxes = [
			1 => new UserTaxes('User#1', 5, 10),
			2 => new UserTaxes('User#2', 7, 15),
			3 => new UserTaxes('User#3', 10, 20),
		];
	}

	public function findUser(int $user_id) 
	{
		if (isset($this->userTaxes[$user_id])) {
			return $this->userTaxes[$user_id];
		}
		return new NullUserTaxes();
	}

}

// Класс работающий с аккаунтами пользователей
//
// Иллюстрация клиентского класса, в котором используется паттерн
class Account 
{
	protected $userId;
	protected $taxesRepository;
	
	public function __construct(int $user_id, TaxesRepository $taxesRepository)
	{
		$this->userId = $user_id;
		$this->taxesRepository = $taxesRepository;
	}
	
	public function printTaxes(): string
	{
		$userTaxes = $this->taxesRepository->findUser($this->userId);
		
		// Debug
		echo "DEBUG===>\n";
			var_dump($userTaxes);
		echo "<===DEBUG\n";
		
		$calcIncomeTax = $calcPropertyTax = 0;
		$userName = '';
		// Из-за свойства isNull() паттерн превратился в анти-паттерн, так как 
		// логику выбора пришлось писать в клиентском классе. 
		if(!$userTaxes->isNull()){
			$taxes = $userTaxes->getUserTaxes();
			$userName = $taxes->account;
			$calcIncomeTax = $taxes->income_tax;
			$calcPropertyTax = $taxes->property_tax;
		} else {
			$userName = "unknown";
		}

		return "Taxes for {$userName}.\n" 
		. "IncomeTax: $calcIncomeTax RUB, PropertyTax: $calcPropertyTax RUB\n";
	}
}

// Клиентский код
$account = new Account(1, new TaxesRepository());
echo $account->printTaxes();
echo "\n\n";
$account = new Account(4, new TaxesRepository());
echo $account->printTaxes();
