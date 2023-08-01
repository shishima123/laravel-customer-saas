<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\ActiveStatus;

class CreateDbConnectionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'db_connections';

    /**
     * Run the migrations.
     * @table db_connections
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->foreignUuid('customer_id')->index();
            $table->string('name', 45)->nullable();
            $table->string('value');
            $table->tinyInteger('active')->default(ActiveStatus::ACTIVE->value);
            $table->softDeletes();
            $table->timestamps();
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
