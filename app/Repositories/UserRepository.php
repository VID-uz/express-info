<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Get all users
     *
     * @return mixed
     */
    public function all()
    {
        return User::all();
    }

    /**
     * Get user by it's id
     *
     * @param int $userId
     * @return User
     */
    public function get($userId)
    {
        return User::findOrFail($userId);
    }

    /**
     * Create an User
     *
     * @param \Illuminate\Http\Request $userData
     * @param int $userRoleId
     * @return void
     */
    public function create($userData, $userRoleId)
    {
        $role = Role::findOrFail($userRoleId);
        $data = [
            'name' => $userData->get('name'),
            'email' => $userData->get('email'),
            'password' => Hash::make($userData->get('password'))
        ];
        $user = User::create($data);
        $user->roles()->attach($role);
        $user->save();
        $user->uploadImage($userData->file('image'));
    }

    /**
     * Update an user
     *
     * @param int $userId
     * @param \Illuminate\Http\Request $userData
     * @return void
     */
    public function update($userId, $userData)
    {
        $user = $this->get($userId);
        $data = $userData->all();
        if (isset($data['birthday_date']))
            $data['birthday_date'] = Carbon::create($data['birthday_date'])->format('Y-m-d');
        $user->update($data);
        $user->generateSlug();
        $user->uploadImage($userData->file('image'));
    }

    /**
     * Delete user
     *
     * @param int $userId
     * @return void
     */
    public function delete($userId)
    {
        User::destroy($userId);
    }

    /**
     * Get all users with role 'admin'
     *
     * @return mixed
     */
    public function getAdmins()
    {
        $role = Role::where('name', 'admin')->first();
        return $role->users;
    }

    /**
     * Get all users with role 'customer'
     *
     * @return mixed
     */
    public function getCustomers()
    {
        $role = Role::where('name', 'customer')->first();
        return $role->users;
    }

    /**
     * Get all roles
     *
     * @return array
     */
    public function allRoles()
    {
        return Role::all();
    }

    /**
     * @inheritDoc
     */
    public function getContractors()
    {
        $allUsers = User::all();
        return $allUsers->filter(function ($user) { return $user->hasRole('contractor'); });
    }

    /**
     * @inheritDoc
     */
    public function getContractorBySlug(string $slug)
    {
        return $this->getContractors()->first(function ($user) use ($slug) {
            return $user->slug === $slug;
        });
    }

    /**
     * @inheritDoc
     */
    public function createAccount($data)
    {
        $user = auth()->user();
        $userRole = $data->get('user_role');
        $role = Role::where('name', $userRole)->first();
        $user->roles()->attach($role->id);
        $dataToSet = [];
        $dataToSet['name'] = $data->get($userRole.'_name');
        $dataToSet['phone_number'] = $data->get($userRole.'_phone_number');
        $dataToSet[$userRole.'_type'] = $data->get($userRole.'_type');
        $dataToSet['company_name'] = $data->get($userRole.'_company_name');
        $dataToSet['about_myself'] = $data->get($userRole.'_about_myself');
        if ($userRole == 'contractor') {
            $dataToSet['birthday_date'] = $data->get('birthday_date');
            $dataToSet['gender'] = $data->get('gender');
        }
        $user->update($dataToSet);
        $user->completed = true;
        $user->save();
        $user->generateSlug();
        $user->uploadImage($data->file('image'));
    }
}
