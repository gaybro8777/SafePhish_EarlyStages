<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sent_email', function(Blueprint $table)
		{
			$table->increments('SML_EmailId');
			$table->integer('SML_UserId');
			$table->string('SML_ProjectName');
			$table->dateTime('SML_SentTimestamp');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sent_email');
	}

}
