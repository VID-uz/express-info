<?php


namespace App\Repositories;


use App\Models\NeedType;
use Illuminate\Support\Str;

class NeedTypeRepository implements NeedTypeRepositoryInterface
{

    /**
     * Get all types of needs
     *
     * @return array
     */
    public function all()
    {
        return NeedType::all();
    }

    /**
     * Get type of needs by it's ID
     *
     * @param int $id
     * @return NeedType
     */
    public function get($id)
    {
        return NeedType::findOrFail($id);
    }

    /**
     * Create a type of need
     *
     * @param \Illuminate\Http\Request $needTypeData
     * @return void
     */
    public function create($needTypeData)
    {
        $needType = NeedType::create($needTypeData->all());
        if (empty($needType->ru_slug))
            $needType->generateSlug();
    }

    /**
     * Update type of needs
     *
     * @param int $id
     * @param \Illuminate\Http\Request $needTypeData
     * @return void
     */
    public function update($id, $needTypeData)
    {
        $needType = $this->get($id);
        $needType->update($needTypeData->all());
        if (empty($needType->ru_slug))
            $needType->generateSlug();
    }

    /**
     * Delete type of needs
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        NeedType::destroy($id);
    }

    /**
     * Get type of needs by slug
     *
     * @param string $slug
     * @return NeedType
     */
    public function getBySlug(string $slug)
    {
        $needType = NeedType::where('ru_slug', $slug)->first();
        return $needType;
    }
}
