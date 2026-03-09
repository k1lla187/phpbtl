<?php
/**
 * EmailService - Gửi email qua SMTP
 * Hỗ trợ Gmail, Outlook, và các SMTP server khác
 */

class EmailService {
    private $host;
    private $port;
    private $secure;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;
    private $charset;
    private $debug;
    private $lastError;
    private $socket;
    
    public function __construct() {
        // Load cấu hình email
        require_once __DIR__ . '/../config/email.php';
        
        $config = EMAIL_CONFIG;
        $this->host = $config['smtp_host'];
        $this->port = $config['smtp_port'];
        $this->secure = $config['smtp_secure'];
        $this->username = $config['smtp_username'];
        $this->password = $config['smtp_password'];
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
        $this->charset = $config['charset'] ?? 'UTF-8';
        $this->debug = $config['debug'] ?? false;
        $this->lastError = '';
    }
    
    /**
     * Gửi email
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề email
     * @param string $body Nội dung email (HTML)
     * @param string $toName Tên người nhận (tùy chọn)
     * @return bool
     */
    public function send($to, $subject, $body, $toName = '') {
        try {
            // Kết nối đến SMTP server
            $this->connect();
            
            // Gửi EHLO
            $this->sendCommand('EHLO ' . gethostname());
            
            // Bắt đầu TLS nếu cần
            if ($this->secure === 'tls') {
                $this->sendCommand('STARTTLS');
                stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->sendCommand('EHLO ' . gethostname());
            }
            
            // Xác thực
            $this->sendCommand('AUTH LOGIN');
            $this->sendCommand(base64_encode($this->username));
            $this->sendCommand(base64_encode($this->password));
            
            // Người gửi
            $this->sendCommand('MAIL FROM:<' . $this->fromEmail . '>');
            
            // Người nhận
            $this->sendCommand('RCPT TO:<' . $to . '>');
            
            // Bắt đầu gửi dữ liệu
            $this->sendCommand('DATA');
            
            // Tạo headers và body của email
            $headers = $this->buildHeaders($to, $subject, $toName);
            $message = $headers . "\r\n" . $body . "\r\n.";
            
            $this->sendCommand($message);
            
            // Đóng kết nối
            $this->sendCommand('QUIT');
            fclose($this->socket);
            
            return true;
            
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            if ($this->debug) {
                error_log("Email Error: " . $e->getMessage());
            }
            if ($this->socket) {
                fclose($this->socket);
            }
            return false;
        }
    }
    
    /**
     * Kết nối đến SMTP server
     */
    private function connect() {
        $protocol = ($this->secure === 'ssl') ? 'ssl://' : '';
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $this->socket = stream_socket_client(
            $protocol . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$this->socket) {
            throw new Exception("Không thể kết nối đến SMTP server: $errstr ($errno)");
        }
        
        // Đọc response ban đầu
        $this->getResponse();
    }
    
    /**
     * Gửi lệnh SMTP
     */
    private function sendCommand($command) {
        if ($this->debug) {
            error_log("SMTP > " . substr($command, 0, 100));
        }
        
        fwrite($this->socket, $command . "\r\n");
        $response = $this->getResponse();
        
        if ($this->debug) {
            error_log("SMTP < " . $response);
        }
        
        // Kiểm tra response code
        $code = substr($response, 0, 3);
        if (!in_array($code, ['220', '221', '235', '250', '251', '334', '354'])) {
            throw new Exception("SMTP Error: $response");
        }
        
        return $response;
    }
    
    /**
     * Đọc response từ server
     */
    private function getResponse() {
        $response = '';
        stream_set_timeout($this->socket, 30);
        
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            // Nếu ký tự thứ 4 là space thì đây là dòng cuối
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        
        return trim($response);
    }
    
    /**
     * Tạo headers cho email
     */
    private function buildHeaders($to, $subject, $toName = '') {
        $boundary = md5(uniqid(time()));
        
        $headers = [];
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'From: =?UTF-8?B?' . base64_encode($this->fromName) . '?= <' . $this->fromEmail . '>';
        
        if ($toName) {
            $headers[] = 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $to . '>';
        } else {
            $headers[] = 'To: ' . $to;
        }
        
        $headers[] = 'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=' . $this->charset;
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'X-Mailer: UNISCORE Mailer';
        $headers[] = 'X-Priority: 1';
        $headers[] = '';
        
        return implode("\r\n", $headers);
    }
    
    /**
     * Lấy lỗi cuối cùng
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Kiểm tra cấu hình email đã được thiết lập chưa
     */
    public function isConfigured() {
        return $this->username !== 'your-email@gmail.com' 
            && $this->password !== 'your-app-password'
            && !empty($this->username)
            && !empty($this->password);
    }
}
