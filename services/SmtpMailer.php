<?php
class SmtpMailer {
    private string $host,$secure,$user,$pass,$fromEmail,$fromName;
    private int $port;
    private $socket=false;
    public function __construct(string $host,int $port,string $secure,string $user,string $pass,string $fromEmail,string $fromName){
        $this->host=$host;$this->port=$port;$this->secure=strtolower($secure);
        $this->user=$user;$this->pass=$pass;$this->fromEmail=$fromEmail;$this->fromName=$fromName;
    }
    public function send(string $toEmail,string $toName,string $subject,string $html,string $text=''): void {
        $this->connect();$this->auth();$this->mail($toEmail,$toName,$subject,$html,$text);$this->quit();
    }
    private function connect(): void {
        $ctx=stream_context_create(['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=>true]]);
        $host=$this->secure==='ssl'?"ssl://{$this->host}":$this->host;
        $this->socket=stream_socket_client("$host:{$this->port}",$e,$es,15,STREAM_CLIENT_CONNECT,$ctx);
        if(!$this->socket) throw new RuntimeException("SMTP: $es");
        stream_set_timeout($this->socket,15);$this->expect(220);
        $this->cmd("EHLO ".gethostname());$ehlo=$this->read();
        if($this->secure==='tls'){
            $this->cmd("STARTTLS");$this->expect(220);
            stream_socket_enable_crypto($this->socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->cmd("EHLO ".gethostname());$this->read();
        }
    }
    private function auth(): void {
        $this->cmd("AUTH LOGIN");$this->expect(334);
        $this->cmd(base64_encode($this->user));$this->expect(334);
        $this->cmd(base64_encode($this->pass));$this->expect(235);
    }
    private function mail(string $to,string $toName,string $subj,string $html,string $text): void {
        $b='==MIME_'.md5(uniqid('',true));$plain=$text?:strip_tags($html);
        $this->cmd("MAIL FROM:<{$this->fromEmail}>");$this->expect(250);
        $this->cmd("RCPT TO:<$to>");$this->expect(250);
        $this->cmd("DATA");$this->expect(354);
        $m ="Date: ".date('r')."\r\nFrom: ".$this->enc($this->fromName)." <{$this->fromEmail}>\r\n";
        $m.="To: ".$this->enc($toName)." <$to>\r\nSubject: ".$this->enc($subj)."\r\n";
        $m.="MIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"$b\"\r\n\r\n";
        $m.="--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($plain))."\r\n";
        $m.="--$b\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($html))."\r\n--$b--\r\n.";
        $this->cmd($m);$this->expect(250);
    }
    private function quit(): void { if($this->socket){$this->cmd("QUIT");fclose($this->socket);$this->socket=false;} }
    private function cmd(string $c): void { fwrite($this->socket,$c."\r\n"); }
    private function read(): string {
        $r=''; while($l=fgets($this->socket,512)){$r.=$l;if(isset($l[3])&&$l[3]===' ')break;} return $r;
    }
    private function expect(int $code): string {
        $r=$this->read();$g=(int)substr($r,0,3);
        if($g!==$code) throw new RuntimeException("Expected $code got $g: ".trim($r));
        return $r;
    }
    private function enc(string $s): string {
        if(!preg_match('/[^\x20-\x7E]/',$s)) return "\"$s\"";
        return '=?UTF-8?B?'.base64_encode($s).'?=';
    }
}
