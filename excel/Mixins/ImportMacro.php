<?php

namespace ChandraHemant\HtkcUtilsExcel\Mixins;

use Illuminate\Database\Eloquent\Model;
use ChandraHemant\HtkcUtilsExcel\Concerns\Importable;
use ChandraHemant\HtkcUtilsExcel\Concerns\ToModel;
use ChandraHemant\HtkcUtilsExcel\Concerns\WithHeadingRow;

class ImportMacro
{
    public function __invoke()
    {
        return function (string $filename, string $disk = null, string $readerType = null) {
            $import = new class(get_class($this->getModel())) implements ToModel, WithHeadingRow
            {
                use Importable;

                /**
                 * @var string
                 */
                private $model;

                /**
                 * @param  string  $model
                 */
                public function __construct(string $model)
                {
                    $this->model = $model;
                }

                /**
                 * @param  array  $row
                 * @return Model|Model[]|null
                 */
                public function model(array $row)
                {
                    return (new $this->model)->fill($row);
                }
            };

            return $import->import($filename, $disk, $readerType);
        };
    }
}
