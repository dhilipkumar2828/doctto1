<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_api_model extends CI_Model {

    public function get_plans($type) {
        $this->db->where('plan_type', $type);
        $this->db->where('is_active', 1);
        return $this->db->get('subscription_plans')->result();
    }

    public function get_my_subscription($id, $type) {
        if ($type == 'doctor') {
            $this->db->select('ds.*, sp.name as plan_name, sp.perks');
            $this->db->from('doctor_subscriptions ds');
            $this->db->join('subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id');
            $this->db->where('ds.doctor_id', $id);
            $this->db->where('ds.status', 'active');
            $this->db->order_by('ds.id', 'DESC');
            return $this->db->get()->row();
        } else {
            $this->db->select('us.*, sp.name as plan_name, sp.description');
            $this->db->from('user_subscriptions us');
            $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
            $this->db->where('us.user_id', $id);
            $this->db->where('us.status', 'active');
            $this->db->order_by('us.id', 'DESC');
            return $this->db->get()->row();
        }
    }

    public function buy_subscription($data) {
        $type = $data['type'];
        unset($data['type']);
        
        if ($type == 'doctor') {
            // First deactivate existing active subscriptions
            $this->db->where('doctor_id', $data['doctor_id']);
            $this->db->where('status', 'active');
            $this->db->update('doctor_subscriptions', ['status' => 'inactive']);
            
            return $this->db->insert('doctor_subscriptions', [
                'doctor_id' => $data['doctor_id'],
                'doctor_subscription_plan_id' => $data['plan_id'],
                'status' => 'active',
                'start_at' => date('Y-m-d H:i:s'),
                'end_at' => date('Y-m-d H:i:s', strtotime('+' . $data['duration'] . ' days')),
                'amount' => $data['amount'],
                'payment_id' => $data['payment_id'] ?? null,
                'payment_status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->where('user_id', $data['user_id']);
            $this->db->where('status', 'active');
            $this->db->update('user_subscriptions', ['status' => 'inactive']);

            return $this->db->insert('user_subscriptions', [
                'user_id' => $data['user_id'],
                'plan_id' => $data['plan_id'],
                'status' => 'active',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+' . $data['duration'] . ' days')),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    public function get_history($id, $type) {
        if ($type == 'doctor') {
            $this->db->select('ds.*, sp.name as plan_name');
            $this->db->from('doctor_subscriptions ds');
            $this->db->join('subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id');
            $this->db->where('ds.doctor_id', $id);
            $this->db->order_by('ds.id', 'DESC');
            return $this->db->get()->result();
        } else {
            $this->db->select('us.*, sp.name as plan_name');
            $this->db->from('user_subscriptions us');
            $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
            $this->db->where('us.user_id', $id);
            $this->db->order_by('us.id', 'DESC');
            return $this->db->get()->result();
        }
    }

    public function cancel_subscription($id, $type) {
        if ($type == 'doctor') {
            $this->db->where('doctor_id', $id);
            $this->db->where('status', 'active');
            return $this->db->update('doctor_subscriptions', ['status' => 'cancelled']);
        } else {
            $this->db->where('user_id', $id);
            $this->db->where('status', 'active');
            return $this->db->update('user_subscriptions', ['status' => 'cancelled']);
        }
    }

    public function get_plan_details($id) {
        return $this->db->get_where('subscription_plans', ['id' => $id])->row();
    }
}
