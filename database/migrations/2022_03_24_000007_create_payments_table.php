<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'payments';

    /**
     * Run the migrations.
     * @table payments
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 45)->primary();
            $table->foreignUuid('customer_id')->index();
            $table->string('type');
            $table->decimal('amount', 16, 2)->nullable();
            $table->tinyInteger('captured')->nullable();
            $table->dateTime('charge_date')->nullable();
            $table->string('stripe_id')->nullable();
            $table->string('description')->nullable();
            $table->string('failure_code', 45)->nullable();
            $table->string('failure_message')->nullable();
            $table->string('invoice_id')->nullable();
            $table->tinyInteger('paid')->nullable();
            $table->string('payment_method', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->decimal('amount_refunded', 16, 2)->nullable();
            $table->string('currency', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
