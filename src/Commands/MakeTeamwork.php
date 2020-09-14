<?php

namespace Mpociot\Teamwork\Commands;

use Illuminate\Console\Command;
use Mpociot\Teamwork\Traits\DetectNamespace;

/**
 * This file is part of Teamwork
 *
 * PHP version 7.2
 *
 * @category PHP
 * @package  Teamwork
 * @author   Marcel Pociot <m.pociot@gmail.com>
 * @license  MIT
 * @link     http://github.com/mpociot/teamwork
 */
class MakeTeamwork extends Command
{

    use DetectNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:teamwork {--views : Only scaffold the teamwork views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Teamwork scaffolding files.';

    protected $views = [
        'emails/invite-member.blade.php' => 'teamwork/emails/invite-member.blade.php',
        'emails/resend-invite.blade.php' => 'teamwork/emails/resend-invite.blade.php',
        'members/list.blade.php' => 'teamwork/members/list.blade.php',
        'create.blade.php' => 'teamwork/create.blade.php',
        'edit.blade.php' => 'teamwork/edit.blade.php',
        'index.blade.php' => 'teamwork/index.blade.php',
        'includes/messages.blade.php' => 'teamwork/includes/messages.blade.php',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        if (! $this->option('views')) {
            $this->info('Installed TeamController.');
            file_put_contents(
                app_path('Http/Controllers/Teamwork/TeamController.php'),
                $this->compileControllerStub('TeamController')
            );

            $this->info('Installed TeamMemberController.');
            file_put_contents(
                app_path('Http/Controllers/Teamwork/TeamMemberController.php'),
                $this->compileControllerStub('TeamMemberController')
            );

            $this->info('Installed AuthController.');
            file_put_contents(
                app_path('Http/Controllers/Teamwork/AuthController.php'),
                $this->compileControllerStub('AuthController')
            );

            $this->info('Installed TeamSwitchController.');
            file_put_contents(
                app_path('Http/Controllers/Teamwork/TeamSwitchController.php'),
                $this->compileControllerStub('TeamSwitchController')
            );

            $this->info('Installed TeamInviteController.');
            file_put_contents(
                app_path('Http/Controllers/Teamwork/TeamInviteController.php'),
                $this->compileControllerStub('TeamInviteController')
            );

            $this->info('Installed JoinTeamListener');
            file_put_contents(
                app_path('Listeners/Teamwork/JoinTeamListener.php'),
                str_replace(
                    '{{namespace}}',
                    $this->getAppNamespace(),
                    file_get_contents(__DIR__ . '/../../stubs/listeners/JoinTeamListener.stub')
                )
            );

            $this->info('Installed TeamInviteMember Mailable Class.');
            file_put_contents(
                app_path('Mail/Teamwork/TeamInviteMember.php'),
                $this->compileMailableStub('TeamInviteMember')
            );

            $this->info('Installed TeamResendInvite Mailable Class.');
            file_put_contents(
                app_path('Mail/Teamwork/TeamResendInvite.php'),
                $this->compileMailableStub('TeamResendInvite')
            );

            $this->info('Installed TeamStoreRequest.');
            file_put_contents(
                app_path('Http/Requests/Teamwork/TeamStoreRequest.php'),
                $this->compileRequestStub('TeamStoreRequest')
            );

            $this->info('Installed TeamUpdateRequest.');
            file_put_contents(
                app_path('Http/Requests/Teamwork/TeamUpdateRequest.php'),
                $this->compileRequestStub('TeamUpdateRequest')
            );

            $this->info('Installed TeamInviteRequest.');
            file_put_contents(
                app_path('Http/Requests/Teamwork/TeamInviteRequest.php'),
                $this->compileRequestStub('TeamInviteRequest')
            );

            $this->info('Updated Routes File.');
            file_put_contents(
               // app_path('Http/routes.php'),
               base_path('routes/web.php'),
                file_get_contents(__DIR__.'/../../routes/routes.php'),
                FILE_APPEND
            );

            $this->info('Installed Team Model.');
            file_put_contents(
                app_path('Models/Team.php'),
                $this->compileModelStub('Team')
            );
        }
        $this->comment('Teamwork scaffolding generated successfully!');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir(app_path('Http/Controllers/Teamwork'))) {
            mkdir(app_path('Http/Controllers/Teamwork'), 0755, true);
        }
        if (! is_dir(app_path('Listeners/Teamwork'))) {
            mkdir(app_path('Listeners/Teamwork'), 0755, true);
        }
        if (! is_dir(app_path('Http/Requests/Teamwork'))) {
            mkdir(app_path('Http/Requests/Teamwork'), 0755, true);
        }
        if (! is_dir(app_path('Mail/Teamwork'))) {
            mkdir(app_path('Mail/Teamwork'), 0755, true);
        }
        if (! is_dir(app_path('Models'))) {
            mkdir(app_path('Models'), 0755, true);
        }
        if (! is_dir(base_path('resources/views/teamwork'))) {
            mkdir(base_path('resources/views/teamwork'), 0755, true);
        }
        if (! is_dir(base_path('resources/views/teamwork/emails'))) {
            mkdir(base_path('resources/views/teamwork/emails'), 0755, true);
        }
        if (! is_dir(base_path('resources/views/teamwork/members'))) {
            mkdir(base_path('resources/views/teamwork/members'), 0755, true);
        }
        if (!is_dir(base_path('resources/views/teamwork/includes'))) {
            mkdir(base_path('resources/views/teamwork/includes'), 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            $path = base_path('resources/views/'.$value);
            $this->line('<info>Created View:</info> '.$path);
            copy(__DIR__.'/../../stubs/resources/views/'.$key, $path);
        }
    }

    /**
     * Compiles the HTTP controller stubs.
     *
     * @param $stubName
     * @return string
     */
    protected function compileControllerStub($stubName)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/../../stubs/controllers/'.$stubName.'.stub')
        );
    }

    /**
     * Compiles the HTTP request stubs.
     *
     * @param $stubName
     * @return string
     */
    protected function compileRequestStub($stubName)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/../../stubs/requests/'.$stubName.'.stub')
        );
    }

    /**
     * Compile the model stub.
     *
     * @param $stubName
     * @return string
     */
    protected function compileModelStub($stubName)
    {
        return str_replace(
            '{{ namespace }}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/../../stubs/models/'.$stubName.'.stub')
        );
    }

    /**
     * Compile the mailable stub.
     *
     * @param $stubName
     * @return string
     */
    protected function compileMailableStub($stubName)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/../../stubs/mail/'.$stubName.'.stub')
        );
    }
}
