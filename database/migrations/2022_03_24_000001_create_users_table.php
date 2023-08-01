<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\ActiveStatus;
use App\Enums\UserChangeInfo;
use App\Enums\UserChangePassword;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id');
            $table->string('email', 45);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->tinyInteger('status')->default(ActiveStatus::ACTIVE->value);
            $table->foreignUuid('userable_id')->nullable()->index();
            $table->string('userable_type', 45)->nullable();
            $table->string('role', 45)->nullable();
            $table->tinyInteger('is_changed_password')->default(UserChangePassword::NO_CHANGE->value);
            $table->tinyInteger('is_changed_info')->default(UserChangeInfo::NO_CHANGE->value);
            $table->string('language')->default('en');
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
