<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $thumbDir = storage_path('app/public/thumbnails');
        $thumbnails = collect(glob($thumbDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE))
            ->shuffle()
            ->values();

        $thumbnailIndex = 0;

        $faker = \Faker\Factory::create();

        DB::beginTransaction();
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('12345678'),
            ]);
            $user->refresh();
            echo "Created user: {$user->id}\n";

            $videoCount = rand(1, 10);
            for ($j = 1; $j <= $videoCount; $j++) {
                if ($thumbnailIndex >= $thumbnails->count()) break;
                try {
                    $title = $faker->catchPhrase();
                    $description = $faker->paragraph(3);
                    $video = Video::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'description' => $description,
                        'thumbnail_path' => 'thumbnails/' . basename($thumbnails[$thumbnailIndex++]),
                        'video_path' => 'videos/sample.mp4',
                    ]);
                    echo "Using user_id: {$user->id}\n";
                    echo "Created video: {$video->title}\n";
                } catch (\Throwable $e) {
                    echo "Failed to create video for user {$user->id}: ".$e->getMessage()."\n";
                }
            }
        }
        // Collect all user and video IDs for relations
        $userIds = User::pluck('id')->all();
        $videoIds = Video::pluck('id')->all();

        // Playlists: Each user gets 1-2 playlists with random videos
        foreach (User::all() as $user) {
            $playlistCount = rand(1, 2);
            for ($p = 1; $p <= $playlistCount; $p++) {
                $playlist = \App\Models\Playlist::create([
                    'user_id' => $user->id,
                    'name' => "{$user->name}'s Playlist $p",
                ]);
                $videosForPlaylist = collect($videoIds)->shuffle()->take(rand(2, 6));
                foreach ($videosForPlaylist as $order => $vid) {
                    $playlist->videos()->attach($vid, ['order_position' => $order + 1]);
                }
            }
        }

        // Likes/Dislikes: Each video gets random likes/dislikes from users
        foreach (Video::all() as $video) {
            $likers = collect($userIds)->shuffle()->take(rand(0, 5));
            $dislikers = collect($userIds)->diff($likers)->shuffle()->take(rand(0, 2));
            $video->likes()->attach($likers);
            $video->dislikes()->attach($dislikers);
        }

        // Subscriptions: Each user subscribes to 1-3 other users
        foreach (User::all() as $user) {
            $others = collect($userIds)->diff([$user->id])->shuffle()->take(rand(1, 3));
            foreach ($others as $subId) {
                \App\Models\Subscription::create([
                    'user_id' => $user->id,
                    'channel_id' => $subId,
                ]);
            }
        }

        // Linked Accounts: Each user links 0-2 accounts
        foreach (User::all() as $user) {
            $linkCount = rand(0, 2);
            $otherUsers = collect($userIds)->diff([$user->id])->shuffle()->take($linkCount);
            foreach ($otherUsers as $linkedId) {
                \App\Models\LinkedAccount::create([
                    'primary_user_id' => $user->id,
                    'linked_user_id' => $linkedId,
                    'linked_at' => now(),
                ]);
            }
        }

        // Video History: Each user watches 2-5 random videos
        foreach (User::all() as $user) {
            $watchedVideos = collect($videoIds)->shuffle()->take(rand(2, 5));
            foreach ($watchedVideos as $vid) {
                \App\Models\VideoHistory::create([
                    'user_id' => $user->id,
                    'video_id' => $vid,
                    'watched_at' => now()->subMinutes(rand(1, 10000)),
                ]);
            }
        }
        DB::commit();
        echo "Videos after seeding: ".Video::count()."\n";
    }
}
