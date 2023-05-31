<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;

class CustomersFilter
{
    protected $safeParms = [
        'name' => ['eq'],
        'type' => ['eq'],
        'email' => ['eq'],
        'address' => ['eq'],
        'city' => ['eq'],
        'state' => ['eq'],
        'postalCode' => ['eq', 'gt', 'lt'],
    ];

    protected $columnMap = [
        'postalCode' => 'postal_code',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];

    public function transform(Request $request)
    {
        $eloQuery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);

            // if there is no query set, just skip the iteration
            if (!isset($query)) {
                continue;
            }

            // checks if the parameter is in column map
            // if in column map, return column map
            // else return the default parameter

            // null coalescing operator (??)
            // 0 ?? 42      # returns 9
            // null ?? 42   $ returns 42
            $column = $this->columnMap['$parm'] ?? $parm;

            // iterate on all of the operators
            foreach ($operators as $operator) {

                // if there is value
                if (isset($query[$operator])) {

                    // eloQuery is congruent to where operator parameters
                    // [['column', 'operator', 'value']]
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
}
