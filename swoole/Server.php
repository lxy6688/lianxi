<?php
	class Server {
		protected $server;
		protected $ip;
		protected $port;

		public function __construct($ip,$port){
			$this->ip = $ip;
			$this->port = $port;
		}

		//执行方法
		public function run(){
			//创建server实例
			$this->createServer();

			$this->server->set(array(
				'worker_num' => 5,
				'daemonize'  => false,
			));

			//进行各种方法操作
			$this->server->on("Start",array($this,"onStart"));
			$this->server->on("Connect",array($this,"onConnect"));
			$this->server->on("Receive",array($this,"onReceive"));
			$this->server->on("Close",array($this,"onClose"));

			$this->server->start();
		}

		private function createServer(){
			$this->server = new swoole_server($this->ip,$this->port);
		}

		//服务启动时触发
		public function onStart($serv){
			echo "server start \n";
		}

		//当有客户端连接时触发
		public function onConnect($serv,$fd,$from_id){
			echo "Client: {$fd} connection \n";
		}

		//监听数据接收事件
		public function onReceive($serv,$fd,$from_id,$data){
			echo "get data from client: {$fd}:{$data} \n";
		}

		//客户端断开时触发
		public function onClose($serv,$fd,$from_id){
			echo "client {$fd} closed";
		}
	}

	$server = new Server("127.0.0.1",8899);
	$server->run();