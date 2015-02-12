<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeploysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deploys', function($table)
		{
			$table->increments('id');
			$table->timestamp('submission_time')->nullable();
			$table->string('jira_ticket', 100)->nullable();
			$table->boolean('isStaged');
			$table->boolean('isDeployed');
			$table->boolean('isValidated');
			$table->timestamp('completion_time')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('deploys');
	}

}
