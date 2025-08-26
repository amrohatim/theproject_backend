<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Branch;

class ActiveBranchLicense implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Find the branch
        $branch = Branch::find($value);

        if (!$branch) {
            $fail('The selected branch does not exist.');
            return;
        }

        // Check if the branch has an active license
        if (!$branch->hasActiveLicense()) {
            $licenseStatus = $branch->getLicenseStatus();

            $message = match($licenseStatus) {
                'pending' => 'Cannot create products/services for this branch. The branch license is pending approval.',
                'expired' => 'Cannot create products/services for this branch. The branch license has expired.',
                'rejected' => 'Cannot create products/services for this branch. The branch license has been rejected.',
                default => 'Cannot create products/services for this branch. The branch requires an active license.'
            };

            $fail($message);
        }
    }
}
