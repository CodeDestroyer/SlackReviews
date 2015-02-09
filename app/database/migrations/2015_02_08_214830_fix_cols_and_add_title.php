<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixColsAndAddTitle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reviews', function($table)
		{
			$table->renameColumn('submission_time', 'submitted');
			$table->renameColumn('submission_user', 'request_user');
			$table->renameColumn('repo_hex', 'title_hash');
			$table->renameColumn('submission_comments', 'request_comments');
			$table->string('jira_ticket');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
