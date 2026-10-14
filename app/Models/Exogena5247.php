<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\Exogena5247Fields;

final class Exogena5247 extends ExogenaRecords
{
    public function __construct(Database $database) { parent::__construct($database,'5247',Exogena5247Fields::class); }
}
