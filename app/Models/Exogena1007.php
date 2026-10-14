<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\Exogena1007Fields;

final class Exogena1007 extends ExogenaRecords
{
    public function __construct(Database $database) { parent::__construct($database,'1007',Exogena1007Fields::class); }
}
