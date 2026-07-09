<?php

use App\Models\KategoriModel;
use CodeIgniter\Test\CIUnitTestCase;

final class KategoriModelTest extends CIUnitTestCase
{
    public function testValidationRulesRequireCategoryName(): void
    {
        $model = new KategoriModel();
        $rules = $model->getValidationRules();

        $this->assertArrayHasKey('nama_kategori', $rules);
        $this->assertStringContainsString('required', $rules['nama_kategori']['rules']);
    }
}
