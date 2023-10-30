<?php
namespace App\Http\Livewire;
use App\Tools;
use Auth;
use Laravel\Jetstream\Http\Livewire\NavigationDropdown;
class Navigation extends NavigationDropdown
{
    public string $tool;
    public function links(): array
    {
        $this->tool = Tools::current();
        switch ($this->tool) {
            case Tools::NINJA_FUNNELS:
            if(Auth::user()->role == 'admin'){
                $links = [
                    [
                        'href' => route('connections.index'),
                        'label' => __('Connections'),
                        'active' => request()->routeIs('connections.*')
                    ],
                    [
                        'href' => route('listings.index'),
                        'label' => __('Lists'),
                        'active' => request()->routeIs('listings.*')
                    ],
                    [
                        'href' => route('rules.index'),
                        'label' => __('Rules'),
                        'active' => request()->routeIs('rules.*')
                    ],
                    [
                        'href' => route('invalidemail.index'),
                        'label' => __('Invalid Email'),
                        'active' => request()->routeIs('invalidemail.*')
                    ],
                    [
                        'href' => route('emaillogs.index'),
                        'label' => __('Email Logs'),
                        'active' => request()->routeIs('emaillogs.*')
                    ],
                    [
                        'href' => route('cron.index'),
                        'label' => __('Cron'),
                        'active' => request()->routeIs('cron.*')
                    ]
                ];
                break;
            }
            else{
                $links = [
                    [
                        'href' => route('connections.index'),
                        'label' => __('Connections'),
                        'active' => request()->routeIs('connections.*')
                    ],
                    [
                        'href' => route('listings.index'),
                        'label' => __('Lists'),
                        'active' => request()->routeIs('listings.*')
                    ],
                    [
                        'href' => route('rules.index'),
                        'label' => __('Rules'),
                        'active' => request()->routeIs('rules.*')
                    ]
                ];
                break;
                }
            case Tools::User_Management:
                $links = [
                    [
                        'href' => route('users.index'),
                        'label' => __('Users'),
                        'active' => request()->routeIs('users.*')
                    ],
                    [
                        'href' => route('useremailsetting.index'),
                        'label' => __('User Setting'),
                        'active' => request()->routeIs('useremailsetting.*')
                    ],
                    [
                        'href' => route('globalsetting.index'),
                        'label' => __('Global Setting'),
                        'active' => request()->routeIs('globalsetting.*')
                    ]
                ];
                break;
            case Tools::DRIP_FEED:
                $links = [
                    [
                        'href' => route('connections.index'),
                        'label' => __('Connections'),
                        'active' => request()->routeIs('connections.*')
                    ],
                ];
                break;
            default:
                $links = [];
                break;
        }
        return $links;
    }
    public function tools(): array
    {
        return [
            [
                'key' => 'ninja_funnels',
                'label' => 'Ninja Funnels'
            ],
            [
                'key' => 'drip_feed',
                'label' => 'Drip feed'
            ],
            [
                'key' => 'user_management',
                'label' => 'User Management'
            ]
        ];
    }
    public function selectTool($tool): void
    {
        $this->tool = $tool;
        Tools::switch($tool);
        if (count($this->links())) {
            $this->redirect(head($this->links())['href']);
        }
    }
    public function getSelectedToolProperty()
    {
        $tools = collect($this->tools());
        return $tools->first(fn ($tool) => $tool['key'] === $this->tool) ?? $tools->first();
    }
    public function render()
    {
        return view('navigation-dropdown', [
            'tools' => $this->tools(),
            'links' => $this->links()

        ]);
    }
}