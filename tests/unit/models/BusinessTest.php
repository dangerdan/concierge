<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Timegridio\Concierge\Models\Business;
use Timegridio\Concierge\Presenters\BusinessPresenter;
use Timegridio\Tests\Models\User;

/**
 * @internal
 * @coversNothing
 */
class BusinessTest extends TestCaseDB
{
    use DatabaseTransactions;
    use CreateUser;
    use CreateBusiness;
    use CreateContact;

    /**
     * @test
     */
    public function aBusinessAutomaticallySetsASlugOnCreate()
    {
        $business = $this->createBusiness(['name' => 'My Awesome Biz']);

        $this->assertEquals('my-awesome-biz', $business->slug);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::__construct
     * @test
     */
    public function itCreatesABusiness()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(Business::class, $business);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::__construct
     * @covers \Timegridio\Concierge\Models\Business::save
     * @test
     */
    public function itCreatesABusinessThatAppearsInDb()
    {
        $business = $this->createBusiness();

        $this->assertDatabaseHas('businesses', ['slug' => $business->slug]);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::__construct
     * @covers \Timegridio\Concierge\Models\Business::save
     * @covers \Timegridio\Concierge\Models\Business::setSlugAttribute
     * @test@
     */
    public function itGeneratesSlugFromName()
    {
        $business = $this->createBusiness();

        $slug = Str::slug($business->name);

        $this->assertEquals($slug, $business->slug);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::getPresenterClass
     * @test
     */
    public function itGetsBusinessPresenter()
    {
        $business = $this->createBusiness();

        $businessPresenter = $business->getPresenterClass();

        $this->assertSame(BusinessPresenter::class, $businessPresenter);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::setPhoneAttribute
     * @test
     */
    public function itSetsEmptyPhoneAttribute()
    {
        $business = $this->createBusiness(['phone' => '']);

        $this->assertNull($business->phone);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::setPostalAddressAttribute
     * @test
     */
    public function itSetsEmptyPostalAddressAttribute()
    {
        $business = $this->createBusiness(['postal_address' => '']);

        $this->assertNull($business->postal_address);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::owner
     * @test
     */
    public function itGetsTheBusinessOwner()
    {
        $owner = $this->createUser();

        $business = $this->createBusiness();
        $business->owners()->save($owner);

        $this->assertInstanceOf(User::class, $business->owner());
        $this->assertEquals($owner->name, $business->owner()->name);
    }

    /**
     * @covers \Timegridio\Concierge\Models\Business::owners
     * @test
     */
    public function itGetsTheBusinessOwners()
    {
        $owner1 = $this->createUser();
        $owner2 = $this->createUser();

        $business = $this->createBusiness();

        $business->owners()->save($owner1);
        $business->owners()->save($owner2);

        $this->assertInstanceOf(Collection::class, $business->owners);
        $this->assertCount(2, $business->owners);
    }

    /**
     * @test
     */
    public function itHasHumanresources()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(HasMany::class, $business->humanresources());
    }

    /**
     * @test
     */
    public function itHasBookings()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(HasMany::class, $business->bookings());
    }

    /**
     * @test
     */
    public function itHasServiceTypes()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(HasMany::class, $business->serviceTypes());
    }

    /**
     * @test
     */
    public function itHasACategory()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(BelongsTo::class, $business->category());
    }
}
