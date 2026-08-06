<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_sample_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_sample_request_id');
            $table->integer('product_id')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('um', 50)->nullable();
            $table->string('cantidad', 100)->nullable();
            $table->boolean('pres_ziploc')->nullable()->default(false);
            $table->boolean('pres_whirlpak')->nullable()->default(false);
            $table->boolean('pres_metalizada')->nullable()->default(false);
            $table->boolean('pres_frasco')->nullable()->default(false);
            $table->boolean('pres_bidon')->nullable()->default(false);
            $table->boolean('pres_otro')->nullable()->default(false);
            $table->string('pres_otro_txt')->nullable();
            $table->string('lote_almacen', 100)->nullable();
            $table->string('lote_venta', 100)->nullable();
            $table->boolean('docs_cc')->nullable()->default(false);
            $table->boolean('docs_ft')->nullable()->default(false);
            $table->boolean('docs_hs')->nullable()->default(false);
            $table->boolean('docs_otro')->nullable()->default(false);
            $table->string('docs_otro_txt')->nullable();
            $table->timestamps();

            // Note: foreign key might fail if customer_sample_requests id is bigIncrements but not exactly matched type, 
            // but in the original migration it is bigIncrements('id').
            $table->foreign('customer_sample_request_id', 'fk_csr_items_request_id')
                  ->references('id')
                  ->on('customer_sample_requests')
                  ->onDelete('cascade');
        });

        // Migrate existing data
        $oldRequests = DB::table('customer_sample_requests')->get();
        foreach ($oldRequests as $req) {
            if ($req->product_id) {
                DB::table('customer_sample_request_items')->insert([
                    'customer_sample_request_id' => $req->id,
                    'product_id'                 => $req->product_id,
                    'sku'                        => $req->sku,
                    'um'                         => $req->um,
                    'cantidad'                   => $req->cantidad,
                    'pres_ziploc'                => $req->pres_ziploc,
                    'pres_whirlpak'              => $req->pres_whirlpak,
                    'pres_metalizada'            => $req->pres_metalizada,
                    'pres_frasco'                => $req->pres_frasco,
                    'pres_bidon'                 => $req->pres_bidon,
                    'pres_otro'                  => $req->pres_otro,
                    'pres_otro_txt'              => $req->pres_otro_txt,
                    'lote_almacen'               => $req->lote_almacen,
                    'lote_venta'                 => $req->lote_venta,
                    'docs_cc'                    => $req->docs_cc,
                    'docs_ft'                    => $req->docs_ft,
                    'docs_hs'                    => $req->docs_hs,
                    'docs_otro'                  => $req->docs_otro,
                    'docs_otro_txt'              => $req->docs_otro_txt,
                    'created_at'                 => now(),
                    'updated_at'                 => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sample_request_items');
    }
};
