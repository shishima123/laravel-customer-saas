<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\PremiumStatus;

class CreateCustomersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'customers';

    /**
     * Run the migrations.
     * @table customers
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id');
            $table->string('name', 100)->nullable();
            $table->string('email', 50);
            $table->string('phone_number', 15)->nullable();
            $table->foreignUuid('company_id')->index();
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->dateTime('next_cycle_date')->nullable();
            $table->string('user_number', 45)->nullable();
            $table->string('billing_contact_email', 50)->nullable();
            $table->tinyInteger('is_premium')->default(PremiumStatus::FREE->value);
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
