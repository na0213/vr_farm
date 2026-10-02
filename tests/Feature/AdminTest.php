<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Farm;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'old-password-1']);
    }

    private function makeFarm(): Farm
    {
        $owner = Owner::create(['name' => 'owner', 'email' => 'owner@example.com', 'password' => 'secret-pass']);
        $farm = new Farm(['owner_id' => $owner->id, 'farm_name' => 'テスト牧場', 'catchcopy' => 'c', 'prefecture' => '北海道', 'address' => 'a', 'theme' => 't']);
        $farm->is_published = true;
        $farm->save();

        return $farm;
    }

    public function test_guests_are_sent_to_the_admin_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_logged_in_admin_is_redirected_away_from_login(): void
    {
        $this->actingAs($this->admin, 'admins')
            ->get(route('admin.login'))
            ->assertRedirect('/admin/dashboard');
    }

    public function test_admin_can_change_password(): void
    {
        $this->actingAs($this->admin, 'admins')->get(route('admin.password.edit'))->assertOk();

        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.password.update'), [
                'current_password' => 'old-password-1',
                'password' => 'new-password-22',
                'password_confirmation' => 'new-password-22',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-password-22', $this->admin->fresh()->password));
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.password.update'), [
                'current_password' => 'wrong',
                'password' => 'new-password-22',
                'password_confirmation' => 'new-password-22',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue(Hash::check('old-password-1', $this->admin->fresh()->password));
    }

    public function test_forgot_password_sends_a_link_that_points_to_the_admin_route(): void
    {
        Notification::fake();

        $this->post(route('admin.password.email'), ['email' => 'admin@example.com'])->assertSessionHasNoErrors();

        Notification::assertSentTo($this->admin, ResetPassword::class, function (ResetPassword $n) {
            $mail = $n->toMail($this->admin);

            return str_contains($mail->actionUrl, '/admin/reset-password/');
        });
    }

    public function test_password_reset_completes_and_returns_to_admin_login(): void
    {
        $token = Password::broker('admins')->createToken($this->admin);

        $this->post(route('admin.password.store'), [
            'token' => $token,
            'email' => 'admin@example.com',
            'password' => 'reset-password-33',
            'password_confirmation' => 'reset-password-33',
        ])->assertRedirect(route('admin.login'));

        $this->assertTrue(Hash::check('reset-password-33', $this->admin->fresh()->password));
    }

    public function test_product_can_be_updated_and_deleted(): void
    {
        $farm = $this->makeFarm();
        $product = Product::create([
            'farm_id' => $farm->id,
            'product_name' => '卵',
            'product_info' => '説明',
            'product_link' => 'https://example.com/a',
            'product_image' => null,
        ]);

        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.backend.products.update', $product->id), [
                'product_name' => '放牧卵',
                'product_info' => '新しい説明',
                'product_link' => 'https://example.com/b',
            ])
            ->assertRedirect(route('admin.backend.products.create', ['farm' => $farm->id]));

        $this->assertSame('放牧卵', $product->fresh()->product_name);

        $this->actingAs($this->admin, 'admins')
            ->delete(route('admin.backend.products.destroy', $product->id))
            ->assertRedirect(route('admin.backend.products.create', ['farm' => $farm->id]));

        $this->assertNull(Product::find($product->id));
    }

    public function test_deleting_a_store_returns_to_the_farm_store_page(): void
    {
        $farm = $this->makeFarm();
        $store = Store::create(['farm_id' => $farm->id, 'store_name' => '販売店', 'store_address' => '住所', 'store_link' => 'https://example.com/s']);

        $this->actingAs($this->admin, 'admins')
            ->delete(route('admin.backend.stores.destroy', $store->id))
            ->assertRedirect(route('admin.backend.stores.create', ['farm' => $farm->id]));
    }

    public function test_farm_list_shows_publish_status_and_counts(): void
    {
        $open = $this->makeFarm();
        $closed = new Farm(['owner_id' => $open->owner_id, 'farm_name' => '非公開牧場', 'catchcopy' => 'c', 'prefecture' => '宮崎県', 'address' => 'a', 'theme' => 't']);
        $closed->is_published = false;
        $closed->save();
        Product::create(['farm_id' => $open->id, 'product_name' => '卵', 'product_info' => '説明', 'product_link' => null, 'product_image' => null]);

        $this->actingAs($this->admin, 'admins')
            ->get(route('admin.backend.farms.index'))
            ->assertOk()
            ->assertSee('テスト牧場')
            ->assertSee('公開中')
            ->assertSee('非公開牧場')
            ->assertSee('非公開')
            ->assertSee('商品(1)');
    }

    public function test_article_list_marks_drafts_and_links_published_ones(): void
    {
        foreach ([['公開の記事', true], ['下書きの記事', false]] as [$title, $published]) {
            $a = new \App\Models\Article();
            $a->title = $title;
            $a->article_content = '<p>本文</p>';
            $a->article_images = '[]';
            $a->is_published = $published;
            $a->save();
        }

        $this->actingAs($this->admin, 'admins')
            ->get(route('admin.backend.article.index'))
            ->assertOk()
            ->assertSee('公開の記事')
            ->assertSee('公開中')
            ->assertSee('下書きの記事')
            ->assertSee('下書き');
    }

    public function test_owner_registration_is_validated(): void
    {
        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.owners.store'), ['name' => '', 'email' => 'not-an-email', 'password' => 'short'])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.owners.store'), ['name' => '山田', 'email' => 'yamada@example.com', 'password' => 'long-enough-1'])
            ->assertRedirect(route('admin.backend.owners.index'));

        $this->assertDatabaseHas('owners', ['email' => 'yamada@example.com']);
    }
}
