<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('USR_UserId');
			$table->string('USR_Username')->unique();
			$table->string('USR_Email')->unique();
			$table->string('USR_FirstName');
			$table->string('USR_LastName');
			$table->string('USR_UniqueURLId')->nullable();
			$table->string('USR_Password',255);
			$table->integer('USR_ProjectMostRecent')->nullable();
			$table->integer('USR_ProjectPrevious')->nullable();
			$table->integer('USR_ProjectLast')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}

}
