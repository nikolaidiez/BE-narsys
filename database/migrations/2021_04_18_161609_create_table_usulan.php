<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsulan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usulans', function (Blueprint $table) {
            $table->id();
            $table->biginteger('nim');
            $table->biginteger('nipPA');
            $table->integer('tahun');
            $table->string('judul');
            $table->longText('abstraksi');
            $table->string('status')->nullable();
            $table->string('rekomendasi')->nullable();
            $table->integer('bidangIlmu')->nullable();
            $table->biginteger('pemb01')->nullable();
            $table->biginteger('pemb02')->nullable();
            $table->biginteger('peng01')->nullable();
            $table->biginteger('peng02')->nullable();
            $table->date('tglSemPro')->nullable();
            $table->string('wktSemPro')->nullable();
            $table->string('ruangSemPro')->nullable();
            $table->date('tglSemAkhir')->nullable();
            $table->string('wktSemAkhir')->nullable();
            $table->string('ruangSemAkhir')->nullable();
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
        Schema::dropIfExists('table_usulan');
    }
}
