<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->increments('PRJ_ProjectId');
			$table->string('PRJ_ProjectName');
            $table->string('PRJ_ComplexityType');
            $table->string('PRJ_TargetType');
			$table->string('PRJ_ProjectAssignee');
			$table->date('PRJ_ProjectStart');
			$table->date('PRJ_ProjectLastActive');
			$table->string('PRJ_ProjectStatus');
			$table->integer('PRJ_ProjectTotalUsers');
			$table->integer('PRJ_EmailViews');
			$table->integer('PRJ_WebsiteViews');
			$table->integer('PRJ_ProjectTotalReports');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projects');
	}

}
