/**
 * Teamwork routes
 */
Route::resource('teams', App\Http\Controllers\Teamwork\TeamController::class)
->except('create');

Route::prefix('teams')->group(function () {
    Route::get('teams/switch/{team}', App\Http\Controllers\Teamwork\TeamSwitchController::class)->name('teams.switch');

    Route::get('members/{team}', [App\Http\Controllers\Teamwork\TeamMemberController::class, 'show'])->name('teams.members.show');
    Route::delete('members/{team}/{user}', [App\Http\Controllers\Teamwork\TeamMemberController::class, 'destroy'])->name('teams.members.destroy');

    Route::post('members/{team}', [App\Http\Controllers\Teamwork\TeamInviteController::class, 'store'])->name('teams.members.invite');
    Route::get('members/resend/{invite}', [App\Http\Controllers\Teamwork\TeamInviteController::class, 'update'])->name('teams.members.resend_invite');
    Route::delete('members/{invite}', [App\Http\Controllers\Teamwork\TeamInviteController::class, 'destroy'])->name('teams.members.invite_destroy');

    Route::get('accept/{token}', App\Http\Controllers\Teamwork\AuthController::class)->name('teams.accept_invite');
});
