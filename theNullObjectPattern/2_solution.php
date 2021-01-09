<?php

declare(strict_types = 1);

namespace Istlian\DesignPatterns\NullObject;

// 1. Класс, в котором содержатся информация о налогах
// конкретного пользователя
class UserTaxes
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
}

// 2. Репозиторий, в котором содержатся объекты пользователей
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
		return $this->userTaxes[$user_id];
	}
}

// 3. Клиентский код
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
		
		if (!is_null($userTaxes)) {
			$taxes = $userTaxes->getUserTaxes();
			return "Taxes for {$taxes->account}.\n" 
			. "IncomeTax: {$taxes->income_tax} RUB, PropertyTax: {$taxes->property_tax} RUB\n";
		}
		return '';
	}
}

// Клиентский код
$account = new Account(1, new TaxesRepository());
echo $account->printTaxes();
echo "\n\n";
$account = new Account(4, new TaxesRepository());
echo $account->printTaxes();
