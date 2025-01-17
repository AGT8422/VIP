<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Transaction;

class ChangeStatusColumnToVarcharInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN `status` VARCHAR(191) NOT NULL;");

        Transaction::where('type', 'Stock_Out')
                ->update(['status' => 'final']);

        Transaction::where('type', 'Stock_In')
                ->update(['status' => 'received']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
