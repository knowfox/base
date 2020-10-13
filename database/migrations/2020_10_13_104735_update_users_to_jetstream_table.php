<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UpdateUsersToJetstreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        
            $table->string('name')->change();
            $table->string('email')->unique()->change();
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->foreignId('current_team_id')->nullable();
            $table->text('profile_photo_path')->nullable();
        });

        User::chunk(200, function ($users) {
            foreach ($users as $user) {

                $team_id = DB::table('teams')->insertGetId([
                    'user_id' => $user->id,
                    'name' => $user->name . "'s team",
                    'personal_team' => true,
                    'created_at' => strftime('%Y-%m-%d %H:%M:%S'),
                ]);
                $user->current_team_id = $team_id;
                $user->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
