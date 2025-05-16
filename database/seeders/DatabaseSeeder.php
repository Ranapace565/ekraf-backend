<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sector;
use App\Models\Business;
use App\Models\Gallery;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Event;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin tetap
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        // Buat 10 visitor_logged dan entrepreneur
        User::factory(5)->create(['role' => 'visitor_logged']);
        User::factory(5)->create(['role' => 'entrepreneur']);

        // Buat sektor
        Sector::factory(17)->create();

        // Buat bisnis yang di-assign ke entrepreneur usaha
        $entrepreneurUsers = User::where('role', 'entrepreneur')->get();
        // foreach ($entrepreneurUsers as $user) {
        //     $business = Business::factory()->create(['user_id' => $user->id]);
        //     BusinessGallery::factory(3)->create(['business_id' => $business->id]);
        //     Event::factory(2)->create([
        //         'user_id' => $user->id,
        //         'business_id' => $business->id,
        //     ]);
        // }

        foreach ($entrepreneurUsers as $user) {
            $business = Business::factory()->create(['user_id' => $user->id]);

            // Galeri & Event tetap
            Gallery::factory(3)->create(['business_id' => $business->id]);
            Event::factory(2)->create(['user_id' => $user->id, 'business_id' => $business->id]);

            // Produk
            \App\Models\Product::factory(4)->create(['business_id' => $business->id]);
        }


        // Komentar oleh visitor_logged ke beberapa bisnis
        $visitor_loggedUsers = User::where('role', 'visitor_logged')->get();
        $businesses = Business::all();
        foreach ($visitor_loggedUsers as $user) {
            foreach ($businesses->random(3) as $business) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'business_id' => $business->id,
                ]);
            }
        }

        // Artikel oleh admin
        $admin = User::where('role', 'admin')->first();
        Article::factory(5)->create(['user_id' => $admin->id]);
    }
}
