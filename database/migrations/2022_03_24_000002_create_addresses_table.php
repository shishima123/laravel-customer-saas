<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'addresses';

    /**
     * Run the migrations.
     * @table addresses
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id');
            $table->string('add1');
            $table->string('add2', 45)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->integer('city_id')->nullable()->index();
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
