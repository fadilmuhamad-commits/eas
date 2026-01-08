<?php

namespace Database\Seeders;

use App\Models\M_Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class S_Permission extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // QUEUE (ANTRIAN)
    M_Permission::create(['name' => 'view_queue', 'description' => 'Dapat melihat data antrian']);
    M_Permission::create(['name' => 'manage_queue', 'description' => 'Dapat mengubah, menghapus, dan memberi catatan data antrian']);

    // BOOKING
    M_Permission::create(['name' => 'view_booking', 'description' => 'Dapat melihat data booking']);
    M_Permission::create(['name' => 'manage_booking', 'description' => 'Dapat menghapus data booking']);

    // CUSTOMER (PENGUNJUNG)
    M_Permission::create(['name' => 'view_customer', 'description' => 'Dapat melihat data pengunjung']);

    // HISTORY (RIWAYAT)
    M_Permission::create(['name' => 'view_history', 'description' => 'Dapat melihat riwayat kunjungan']);

    // COUNTER (LOKET)
    M_Permission::create(['name' => 'view_counter', 'description' => 'Dapat melihat data loket']);
    M_Permission::create(['name' => 'manage_counter', 'description' => 'Dapat membuat, mengubah, dan menghapus data loket']);

    // GROUP
    M_Permission::create(['name' => 'view_group', 'description' => 'Dapat melihat data group']);
    M_Permission::create(['name' => 'manage_group', 'description' => 'Dapat membuat, mengubah, dan menghapus data group']);

    // CATEGORY
    M_Permission::create(['name' => 'view_category', 'description' => 'Dapat melihat data kategori loket dan tiket']);
    M_Permission::create(['name' => 'manage_category', 'description' => 'Dapat membuat, mengubah, dan menghapus data kategori loket dan tiket']);

    // TICKET CATEGORY (KATEGORI TIKET)
    // M_Permission::create(['name' => 'view_ticket_category']);
    // M_Permission::create(['name' => 'manage_ticket_category']);

    // USER
    M_Permission::create(['name' => 'view_user', 'description' => 'Dapat melihat data user']);
    M_Permission::create(['name' => 'manage_user', 'description' => 'Dapat membuat, mengubah, dan menghapus data user']);

    // ROLE
    M_Permission::create(['name' => 'view_role', 'description' => 'Dapat melihat data role']);
    M_Permission::create(['name' => 'manage_role', 'description' => 'Dapat membuat, mengubah, dan menghapus data role']);

    // CONFIG
    M_Permission::create(['name' => 'manage_config', 'description' => 'Dapat melihat dan mengubah konfigurasi web']);

    // CALL QUEUE (PANGGIL ANTRIAN)
    M_Permission::create(['name' => 'call_queue', 'description' => 'Dapat memanggil tiket/antrian (User harus memiliki loket)']);

    // GET TICKET
    M_Permission::create(['name' => 'get_ticket', 'description' => 'Dapat mencetak tiket di halaman admin']);

    // ALL COUNTER
    M_Permission::create(['name' => 'view_all_counter', 'description' => 'Dapat melihat semua data tiket/booking/riwayat untuk user yang memiliki loket']);
  }
}
