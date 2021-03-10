<?php


namespace Modules\Payment\Services\Transaction;


use App\Helpers\Methods;
use App\Models\AppConfig;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Payment\Models\BillAction;
use Modules\Payment\Models\BillBalance;
use phpDocumentor\Reflection\Types\This;

class TransactionService
{
    protected $model;

    /**
     * TransactionService constructor.
     * @param BillAction|null $model
     */
    public function __construct(BillAction $model = null)
    {
        $this->model = $model ?? new BillAction();
    }

    /**
     * @return BillAction|null
     */
    public function getModel(): ?BillAction
    {
        return $this->model;
    }

    /**
     * @param BillAction|null $model
     */
    public function setModel(?BillAction $model): void
    {
        $this->model = $model;
    }

    /**
     * @param Model $model
     * @param User|null $user
     * @param float $amount
     * @return $this
     */
    public function make(Model $model, float $amount = 0, User $user = null)
    {
        $this->setModelProperty([
            'rel_type' => get_class($model),
            'rel_id' => $model->id,
            'user_id' => $user->id ?? null,
            'amount' => $amount,
        ]);
        $this->setStatusById(0);
        $this->setActionAdd();
        return $this;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->getModel()->save($options);
        return $this->getModel();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setModelProperty(array $data)
    {
        $this->getModel()->fill($data);
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setOrderId(int $id)
    {
        return $this->setModelProperty([
            'order_id' => $id
        ]);
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        return $this->setModelProperty([
            'user_id' => $user->id
        ]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setUserID(int $id):self
    {
        return $this->setModelProperty([
            'user_id' => $id
        ]);
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setBeneficiary(User $user): self
    {
        return $this->setModelProperty([
            'beneficiary_id' => $user->id
        ]);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setBeneficiaryID(int $id): self
    {
        return $this->setModelProperty([
            'beneficiary_id' => $id
        ]);
    }

    /**
     * @param BillBalance $model
     * @return $this
     */
    public function setBalance(BillBalance $model): self
    {
        return $this->setModelProperty([
            'bill_balance_id' => $model->id
        ]);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setServiceName(string $name): self
    {
        return $this->setModelProperty([
            'service_name' => $name
        ]);
    }

    /**
     * Тип "Оплата услуги"
     * @return $this
     */
    public function setTypePayment(): self
    {
        return $this->setModelProperty([
            'type' => $this->getTypes(0)
        ]);
    }

    /**
     * Тип "Вывод средств"
     * @return $this
     */
    public function setTypeOutput(): self
    {
        return $this->setModelProperty([
            'type' => $this->getTypes(1)
        ]);
    }

    /**
     * Тип "Процент сервиса(наш)"
     * @return $this
     */
    public function setTypePercent(): self
    {
        return $this->setModelProperty([
            'type' => $this->getTypes(3)
        ]);
    }

    /**
     * Тип "Сумма процента"
     * @return $this
     */
    public function setTypePercentAmount(): self
    {
        return $this->setModelProperty([
            'type' => $this->getTypes(4)
        ]);
    }

    /**
     * @param $id
     * @return $this
     */
    public function setTypeByID($id): self
    {
        return $this->setModelProperty([
            'type' => $this->getTypes($id)
        ]);
    }

    /**
     * @return $this
     */
    public function setActionAdd(): self
    {
        return $this->setModelProperty([
            'action' => $this->getActions(0)
        ]);
    }

    /**
     * @return $this
     */
    public function setActionTake()
    {
        return $this->setModelProperty([
            'action' => $this->getActions(1)
        ]);
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount)
    {
        return $this->setModelProperty([
            'amount' => $amount
        ]);
    }

    /**
     * @param Currency $model
     * @return $this
     */
    public function setCurrency(Currency $model)
    {
        return $this->setModelProperty([
            'currency_id' => $model->id
        ]);
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        return $this->setModelProperty([
            'items' => $items
        ]);
    }

    /**
     * @param Carbon $time
     * @return $this
     */
    public function setExpiredTime(Carbon $time)
    {
        return $this->setModelProperty([
            'expired_time' => $time
        ]);
    }

    /**
     * @param int $days
     * @return $this
     */
    public function setExpiredTimeDays(int $days)
    {
        $date = Carbon::now();
        $date->addDays($days);
        return $this->setExpiredTime($date);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setStatusById(int $id)
    {
        return $this->setModelProperty([
            'status' => $this->getStatuses($id)
        ]);
    }

    /**
     * Поиск записи по ид
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        $entry = $this->getModel()->findOrFail($id);
        $this->setModel($entry);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getModel()->toArray();
    }

    /**
     * @param int|null $id
     * @return array|mixed|string|string[]
     */
    public function getStatuses(int $id = null)
    {
        return ($this->getModel()->getStatuses($id));
    }

    /**
     * @param int|null $id
     * @return array|mixed|string|string[]
     */
    protected function getTypes(int $id = null)
    {
        return $this->getModel()->getTypes($id);
    }

    /**
     * @param int|null $id
     * @return array|mixed|string|string[]
     */
    protected function getActions(int $id = null)
    {
        return $this->getModel()->getActions()[$id];
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getModel()->getId();
    }

    /**
     * Переводит или списывает средства со счета
     * @return $this
     */
    public function complete(): self
    {
        return $this->completeByAction($this->getModel());
    }

    /**
     * Переводит или списывает средства со счета
     * @param BillAction $action
     * @return $this
     */
    public function completeByAction(BillAction $action): self
    {
        $tService = new TransactionService($action);
        $transAction = $action->action;
        $transActions = $action->getActions();
        $balanceModel = new BillBalance();
        $balance = null;

        switch ($transAction) {
            case $transActions[0]:
                $balance = $balanceModel->add($action);
                break;
            case $transActions[1]:
                $balance = $balanceModel->take($action);
                break;
        }
//        $tService->setStatusById(4); // FINISHED
//        $tService->save();
        return $this;
    }

    /**
     * @param BillAction $action
     * @param $amount
     * @return $this
     */
    public function makePercentTransaction(BillAction $action, $amount): self
    {
        $transaction = $this->make($action, $amount);
        $transaction->setActionTake();
        $transaction->setTypePercent();
        $transaction->setStatusById(1); // WAITING
        return $transaction;
    }

    /**
     * Создает транзакцию на сумму от записи процента
     * @param BillAction $action
     * @return $this
     */
    public function makePercentAmountTransaction(BillAction $action): self
    {
        if ($action->type !== $action->getTypes(3)) throw new \Exception('The wrong type of action, need user "' + $action->getTypes(3) + '"');
        $paymentTransaction = $action->actionable;
        $amount = (new Methods())->calcPercentAmount($paymentTransaction->amount, $action->amount);
        $transaction = $this->make($action, $amount);
        $transaction->setActionTake();
        $transaction->setTypePercentAmount();
        $transaction->setBeneficiaryID($action->beneficiary_id);
        $transaction->setUserID($action->user_id);
        $transaction->setStatusById(1); // WAITING
        return $transaction;
    }
}
