<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\Exogena1001Fields;

final class Exogena1001 extends ExogenaRecords
{
    public function __construct(Database $database) { parent::__construct($database,'1001',Exogena1001Fields::class); }
}
