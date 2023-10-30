<?php

namespace App\Http\Livewire;

use App\Actions\SendWebhookDummyData;
use App\Actions\TestConnection;
use App\Models\Connection;
use App\Tools;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Auth;
use DB;


class ConnectionList extends Component
{
    use WithPagination;
    public bool $confirmingConnectionDeletion = false;
    public ?Connection $connectionBeingDeleted = null;

    public array $sendDummyData = [];
    public array $testedConnections = [];
    public array $testedMauticConnections = [];

    public function getConnectionsProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return Connection::leftjoin('users', 'users.id', '=', 'connections.user_id')
            ->select('users.first_name as first_name','connections.*')
            ->paginate(10);
        }
        else
        {
            return Connection::where('user_id',Auth::user()->id)->paginate(10);
        }  
    }

    public function testConnection(Connection $connection, TestConnection $tester)
    {
        
           $this->testedConnections[$connection->id] = $tester->test($connection);
    }

    public function testMauticConnection(Connection $connection, TestConnection $tester)
    {
        // $result = $tester->testmauitc($connection);
        
        $this->testedMauticConnections[$connection->id] = $tester->testmauitc($connection);
    }

    public function sendWebhookDummyData(Connection $connection, SendWebhookDummyData $sender): void
    {
        $sender->send($connection->base_url);
        $this->sendDummyData[$connection->id] = true;
    }

    public function confirmConnectionDeletion(Connection $connection): void
    {
        $this->confirmingConnectionDeletion = true;
        $this->connectionBeingDeleted = $connection;
    }

    public function deleteConnection(): void
    {
        $this->connectionBeingDeleted->delete();

        $this->confirmingConnectionDeletion = false;
    }
}
