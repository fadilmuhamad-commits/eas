<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function ($table) {
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::table('users', function ($table) {
            $table->foreign('counter_id')->references('id')->on('counters');
        });

        Schema::table('tickets', function ($table) {
            $table->foreign('counter_id')->references('id')->on('counters');
        });

        Schema::table('tickets', function ($table) {
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('tickets', function ($table) {
            $table->foreign('counter_category_id')->references('id')->on('counter_categories');
        });

        Schema::table('tickets', function ($table) {
            $table->foreign('ticket_category_id')->references('id')->on('ticket_categories');
        });

        Schema::table('config', function ($table) {
            $table->foreign('color1_id')->references('id')->on('colors');
        });

        Schema::table('config', function ($table) {
            $table->foreign('color2_id')->references('id')->on('colors');
        });

        Schema::table('config', function ($table) {
            $table->foreign('color3_id')->references('id')->on('colors');
        });

        Schema::table('counters', function ($table) {
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('counters', function ($table) {
            $table->foreign('color_id')->references('id')->on('colors');
        });

        Schema::table('counter_categories', function ($table) {
            $table->foreign('color_id')->references('id')->on('colors');
        });

        Schema::table('relation_counter_categories', function ($table) {
            $table->foreign('counter_category_id')->references('id')->on('counter_categories')->onDelete('cascade');
        });

        Schema::table('relation_counter_categories', function ($table) {
            $table->foreign('counter_id')->references('id')->on('counters')->onDelete('cascade');
        });

        Schema::table('relation_role_permissions', function ($table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::table('relation_role_permissions', function ($table) {
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::table('queues', function ($table) {
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('user_ratings', function ($table) {
            $table->foreign('ticket_id')->references('id')->on('tickets');
        });

        Schema::table('user_ratings', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
