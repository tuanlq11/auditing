<?php
namespace tuanlq11\auditing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use tuanlq11\Audit;

class AuditingTrait extends Model
{
    /**
     * Turn on|off trait
     * @var bool
     */
    protected $auditingEnabled = true;
    /**
     * List field do keep in database audit
     * @var array
     */
    protected $doKeep = [];

    /**
     * List field not keep in database audit.
     * Stronger doKeep
     *
     * @var array
     */
    protected $dontKeep = [];

    /**
     * Origin value didn't changed
     * @var array
     */
    protected $originalData = [];

    /**
     * Data-value was changed
     * @var array
     */
    protected $updatedData = [];

    /**
     * Limit history per model
     * @var null
     */
    protected $limitHistory = null;

    /**
     * List actionable to auditing
     * @var array
     */
    protected $auditingActionable = ['created', 'updated', 'deleted'];

    public static function bootAuditingTrait()
    {
        self::saving(function ($obj) {
            $obj->prepareAuditing();
        });

        self::created(function ($obj) {
            if (!$obj->auditingEnabled) return;
            $obj->auditing($obj->updatedData, $obj->originalData, 'created');
        });

        self::updated(function ($obj) {
            if (!$obj->auditingEnabled) return;
            $obj->auditing($obj->updatedData, $obj->originalData, 'updated');
        });

        self::deleted(function ($obj) {
            if (!$obj->auditingEnabled) return;
            $obj->auditing($obj->updatedData, $obj->originalData, 'deleted');
        });
    }

    protected function prepareAuditing()
    {
        if (!$this->auditingEnabled) return;

        $this->originalData = $this->original;
        $this->updatedData = $this->attributes;

        foreach ($this->updatedData as $key => $val) {
            if (is_object($val) && !method_exists($val, '__toString')) {
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
                $this->dontKeep[] = $key;
            }
        }
    }

    /**
     * @param $action
     */
    protected function auditing($new_value = [], $old_value = [], $action)
    {
        $data = [
            'model'     => get_class($this),
            'model_id'  => $this->getKey(),
            'user_id'   => $this->getAuditUserID(),
            'new_value' => json_encode($new_value),
            'old_value' => json_encode($old_value),
            'action'    => $action,
        ];

        Audit::create($data);
    }

    /**
     *
     */
    protected function getAuditUserID()
    {
        try {
            if (Auth::check()) {
                return Auth::user()->getAuthIdentifier();
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * @param $action
     *
     * @return bool
     */
    public function isAuditingActionble($action)
    {
        return in_array($action, $this->auditingActionable);
    }

    /**
     * @return bool
     */
    public function isLimitHistoryReach()
    {
        if (is_null($this->limitHistory)) return false;
        return $this->logHistory()->count() >= $this->limitHistory;
    }

    /**
     * @param int    $limit
     * @param string $order
     */
    public static function staticLogHistory($limit = 100, $order = 'desc')
    {
        return Audit::query()->where('model', get_called_class())->orderBy('updated_at', $order)->limit($limit)->get();
    }

    /**
     * @param int    $limit
     * @param string $order
     *
     * @return mixed
     */
    public function logHistory($limit = 100, $order = 'desc')
    {
        return static::staticLogHistory($limit, $order);
    }
}