<?php

namespace App\Notifications;

use App\Models\Indicator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndicatorStatusChangedForAssignees extends Notification
{
    use Queueable;

    public function __construct(
        public Indicator $indicator,
        public int|string $newStatus,
        public int|string|null $prevStatus = null,
        public ?string $changedBy = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    private function statusLabel(int|string|null $status): string
    {
        $map = [
            0 => 'ร่างกำลังดำเนินการ',
            1 => 'ร่าง',
            2 => 'ฉบับจริง (ส่งให้ QA)',
            3 => 'ผ่าน (เสร็จสิ้น)',
            4 => 'ไม่ผ่าน (ต้องแก้ไข)',
        ];
        if (is_numeric($status)) {
            $key = (int) $status;
            return $map[$key] ?? (string) $status;
        }
        return (string) ($status ?? '-');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $indicator = $this->indicator;
        $newLabel = $this->statusLabel($this->newStatus);
        $prevLabel = $this->prevStatus !== null ? $this->statusLabel($this->prevStatus) : null;
        $title = sprintf('[KPI] เปลี่ยนสถานะ: %s %s → %s', (string) ($indicator->code ?? ''), (string) ($indicator->name ?? ''), $newLabel);
        $url = route('dashboardkpi.user.show', ['id' => $indicator->id]);

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting('แจ้งเตือนผู้รับมอบหมาย')
            ->line('มีการเปลี่ยนสถานะตัวชี้วัดของคุณในระบบ KPI')
            ->line(sprintf('ตัวชี้วัด: %s (%s)', (string) ($indicator->name ?? '-'), (string) ($indicator->code ?? '-')))
            ->line('สถานะใหม่: ' . $newLabel)
            ->action('เปิดดูตัวชี้วัด', $url);

        if ($prevLabel) {
            $mail->line('สถานะเดิม: ' . $prevLabel);
        }
        if ($this->changedBy) {
            $mail->line('ปรับโดย: ' . $this->changedBy);
        }

        return $mail->line('ขอบคุณค่ะ/ครับ')->salutation(' ');
    }
}

