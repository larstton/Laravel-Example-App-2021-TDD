<?php

namespace App\Http\Controllers\Recipient;

use App\Actions\Recipient\GetRecipientLogFromNotifierAction;
use App\Http\Controllers\Controller;
use App\Models\Recipient;
use Illuminate\Http\Request;

class RecipientLogController extends Controller
{
    public function __invoke(Request $request, Recipient $recipient, GetRecipientLogFromNotifierAction $action)
    {
        $this->validate($request, [
            'filter.from' => ['required', 'integer'],
            'filter.to'   => ['required', 'integer'],
            'page.size'   => ['sometimes', 'nullable', 'integer'],
        ]);

        $data = $action->execute($recipient, $request->only(['filter', 'page']));

        return $this->json([
            'data' => $data,
        ]);
    }
}
