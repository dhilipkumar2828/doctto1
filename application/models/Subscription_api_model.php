<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription_api_model extends CI_Model
{

    public function get_plans($type)
    {
        $this->db->where('plan_type', $type);
        $this->db->where('is_active', 1);
        $plans = $this->db->get('subscription_plans')->result();
        
        if ($plans) {
            foreach ($plans as $plan) {
                $this->db->select('spd.doctor_id, d.doctor_name, spd.title, spd.app_image');
                $this->db->from('subscription_plan_doctors spd');
                $this->db->join('doctors d', 'd.id = spd.doctor_id', 'left');
                $this->db->where('spd.plan_id', $plan->id);
                $this->db->order_by('spd.sort_order', 'ASC');
                $plan_doctors = $this->db->get()->result();
                
                foreach ($plan_doctors as $doc) {
                    $doc->app_image = !empty($doc->app_image) ? base_url() . 'uploads/doctor_banners/' . $doc->app_image : '';
                }
                
                $plan->plan_doctors = $plan_doctors;
            }
        }
        
        return $plans;
    }

    public function get_popular_plan($type)
    {
        $this->db->where('plan_type', $type);
        $this->db->where('name', 'Popular Plan');
        $this->db->where('is_active', 1);
        return $this->db->get('subscription_plans')->row();
    }

    public function get_plan_by_name($name, $type)
    {
        $this->db->where('plan_type', $type);
        $this->db->where('name', $name);
        $this->db->where('is_active', 1);
        return $this->db->get('subscription_plans')->row();
    }

    public function get_my_subscription($id, $type)
    {
        if ($type == 'doctor') {
            $this->db->select('ds.*, dsp.name as plan_name, dsp.description as perks, dsp.price as plan_price');
            $this->db->from('doctor_subscriptions ds');
            $this->db->join('subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
            $this->db->where('ds.doctor_id', $id);
            $this->db->where('ds.status', 'active');
            $this->db->order_by('ds.id', 'DESC');
            return $this->db->get()->row();
        }
        else {
            $this->db->select('us.*, sp.name as plan_name, sp.description, sp.call_chat, sp.whatsapp_chat, sp.price as plan_price');
            $this->db->from('user_subscriptions us');
            $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
            $this->db->where('us.user_id', $id);
            $this->db->where('us.status', 'active');
            $this->db->order_by('us.id', 'DESC');
            return $this->db->get()->row();
        }
    }

    public function buy_subscription($data)
    {
        $type = $data['type'];
        unset($data['type']);

        if ($type == 'doctor') {
            // Check for current active subscription
            $current = $this->db->get_where('doctor_subscriptions', [
                'doctor_id' => $data['doctor_id'],
                'status' => 'active'
            ])->row();

            // BLOCK: If same plan is already active and NOT expired
            if ($current && $current->doctor_subscription_plan_id == $data['plan_id']) {
                if (strtotime($current->end_at) > time()) {
                    return 'already_active';
                }
            }

            // Deactivate all existing active subscriptions
            $this->db->where('doctor_id', $data['doctor_id']);
            $this->db->where('status', 'active');
            $this->db->update('doctor_subscriptions', ['status' => 'expired']);

            $insert_data = [
                'doctor_id' => $data['doctor_id'],
                'doctor_subscription_plan_id' => $data['plan_id'],
                'status' => 'active',
                'start_at' => date('Y-m-d H:i:s'),
                'end_at' => date('Y-m-d H:i:s', strtotime('+' . $data['duration'] . ' days')),
                'amount' => $data['amount'],
                'payment_id' => $data['payment_id'] ?? null,
                'payment_status' => 'completed',
                'auto_renew' => 1,
                'featured_status' => $data['featured_status'] ?? 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('doctor_subscriptions', $insert_data)) {
                $insert_id = $this->db->insert_id();
                return $this->db->get_where('doctor_subscriptions', ['id' => $insert_id])->row();
            }
        }
        else {
            // Same logic for Customer/User
            $current = $this->db->get_where('user_subscriptions', [
                'user_id' => $data['user_id'],
                'status' => 'active'
            ])->row();

            // BLOCK
            if ($current && $current->plan_id == $data['plan_id']) {
                if (strtotime($current->end_date) > time()) {
                    return 'already_active';
                }
            }

            $this->db->where('user_id', $data['user_id']);
            $this->db->where('status', 'active');
            $this->db->update('user_subscriptions', ['status' => 'expired']);

            $insert_data = [
                'user_id' => $data['user_id'],
                'plan_id' => $data['plan_id'],
                'status' => 'active',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+' . $data['duration'] . ' days')),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('user_subscriptions', $insert_data)) {
                $insert_id = $this->db->insert_id();
                return $this->db->get_where('user_subscriptions', ['id' => $insert_id])->row();
            }
        }
        return false;
    }
    public function get_history($id, $type)
    {
        if ($type == 'doctor') {
            $this->db->select('ds.*, dsp.name as plan_name');
            $this->db->from('doctor_subscriptions ds');
            $this->db->join('subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
            $this->db->where('ds.doctor_id', $id);
            $this->db->order_by('ds.id', 'DESC');
            return $this->db->get()->result();
        }
        else {
            $this->db->select('us.*, sp.name as plan_name');
            $this->db->from('user_subscriptions us');
            $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
            $this->db->where('us.user_id', $id);
            $this->db->order_by('us.id', 'DESC');
            return $this->db->get()->result();
        }
    }

    public function cancel_subscription($id, $type)
    {
        if ($type == 'doctor') {
            $this->db->where('doctor_id', $id);
            $this->db->where('status', 'active');
            return $this->db->update('doctor_subscriptions', ['status' => 'cancelled']);
        }
        else {
            $this->db->where('user_id', $id);
            $this->db->where('status', 'active');
            return $this->db->update('user_subscriptions', ['status' => 'cancelled']);
        }
    }

    public function get_plan_details($id)
    {
        $plan = $this->db->get_where('subscription_plans', ['id' => $id, 'is_active' => 1])->row();
        if (!$plan) {
            $plan = $this->db->get_where('doctor_subscription_plans', ['id' => $id, 'is_active' => 1])->row();
        }
        return $plan;
    }

    public function insert_plan($data)
    {
        $this->db->insert('subscription_plans', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $this->db->get_where('subscription_plans', ['id' => $insert_id])->row();
        }
        return false;
    }

    public function get_subscription_status($user_id, $plan_id, $type)
    {
        if ($type == 'doctor') {
            $this->db->order_by('id', 'DESC');
            $check = $this->db->get_where('doctor_subscriptions', [
                'doctor_id' => $user_id,
                'doctor_subscription_plan_id' => $plan_id
            ])->row();
        }
        else {
            $this->db->order_by('id', 'DESC');
            $check = $this->db->get_where('user_subscriptions', [
                'user_id' => $user_id,
                'plan_id' => $plan_id
            ])->row();
        }
        if ($check) {
            return !empty($check->status) ? $check->status : 'expired';
        }
        return 'not_subscribed';
    }

    public function get_user_subscribed_doctors($subscription_id)
    {
        $this->db->select('usd.*, d.doctor_name, d.doctor_image, d.designations');
        $this->db->from('user_subscribed_doctors usd');
        $this->db->join('doctors d', 'd.id = usd.doctor_id');
        $this->db->where('usd.subscription_id', $subscription_id);
        $this->db->where('usd.status', 'active');
        return $this->db->get()->result();
    }

    public function subscribe_to_doctor($user_id, $subscription_id, $doctor_id, $max_allowed)
    {
        // Check if already subscribed to this doctor in this subscription
        $exists = $this->db->get_where('user_subscribed_doctors', [
            'subscription_id' => $subscription_id,
            'doctor_id' => $doctor_id,
            'status' => 'active'
        ])->row();

        if ($exists) {
            return 'already_subscribed';
        }

        // Check limit
        $current_count = $this->db->where([
            'subscription_id' => $subscription_id,
            'status' => 'active'
        ])->count_all_results('user_subscribed_doctors');

        if ($current_count >= $max_allowed) {
            return 'limit_reached';
        }

        $insert_data = [
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
            'doctor_id' => $doctor_id,
            'status' => 'active',
            'subscribed_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('user_subscribed_doctors', $insert_data);
    }

    public function get_plan_doctors($plan_id = null, $exclude_user_id = null)
    {
        $this->db->select('spd.plan_id, spd.app_image, d.id as doctor_id, d.doctor_name, d.doctor_image, d.designations, d.mobile_number, d.morning_start_time, d.morning_end_time, d.afternoon_start_time, d.afternoon_end_time, d.evening_start_time, d.evening_end_time, d.specialisation, d.specialist_in, d.rating, d.rating_count, d.blue_tick, spd.sort_order, sp.name as plan_name, sp.id as subscription_plan_id, sp.price as plan_price');
        $this->db->from('subscription_plan_doctors spd');
        $this->db->join('doctors d', 'spd.doctor_id = d.id');

        // Join with doctor_subscriptions to verify payment and active status
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = d.id AND ds.status = \'active\'');

        // Join with subscription_plans using the plan_id they were assigned to (spd.plan_id)
        $this->db->join('subscription_plans sp', 'sp.id = spd.plan_id');

        if ($plan_id) {
            $this->db->where('spd.plan_id', $plan_id);
        }

        // Rigor checks: Expiry, Account Status, Featured
        $this->db->where('ds.end_at >=', date('Y-m-d H:i:s'));
        $this->db->where('d.doctor_show_status', 'active');
        $this->db->where('ds.featured_status', 1);

        // Exclude doctors already subscribed by this specific user
        if ($exclude_user_id) {
            $escaped_id = $this->db->escape($exclude_user_id);
            $this->db->where("d.id NOT IN (SELECT doctor_id FROM user_subscribed_doctors usd JOIN user_subscriptions us ON us.id = usd.subscription_id WHERE us.user_id = $escaped_id AND usd.status = 'active' AND us.status = 'active')", NULL, FALSE);
        }

        $this->db->group_by('d.id');
        $this->db->order_by('spd.sort_order', 'ASC');

        $doctors = $this->db->get()->result();
        // print_r($this->db->last_query());
        // die;
        foreach ($doctors as $doc) {
            $doc->app_image = !empty($doc->app_image) ? base_url() . 'uploads/doctor_banners/' . $doc->app_image : '';
            $doc->doctor_image = !empty($doc->doctor_image) ? base_url() . 'uploads/doctors/' . $doc->doctor_image : base_url() . 'uploads/profile-icon-3.png';
            $doc->specialisation_name = $this->get_specialisation_name($doc->specialisation);
            $designation_names = $this->get_designation_names($doc->designations);
            $doc->designation_names = implode(',', $designation_names);
            $doc->rating_count = !empty($doc->rating_count) ? $doc->rating_count : "0";
            $doc->blue_tick = ($doc->blue_tick == 1 || $doc->blue_tick == 'active') ? 'active' : 'inactive';
        }

        return $doctors;
    }


    public function get_all_subscribed_doctors($exclude_user_id = null)
    {
        $this->db->select('ds.id as subscription_id, ds.start_at, ds.end_at, ds.status as subscription_status, d.id as doctor_id, d.doctor_name, d.doctor_image, d.designations, d.mobile_number, d.morning_start_time, d.morning_end_time, d.afternoon_start_time, d.afternoon_end_time, d.evening_start_time, d.evening_end_time, d.specialisation, d.specialist_in, d.rating, d.rating_count, d.blue_tick, sp.name as plan_name, sp.price as plan_price');
        $this->db->from('doctors d');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id');
        $this->db->join('subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id');

        // Filter by subscription status and rigor checks
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.end_at >=', date('Y-m-d H:i:s'));
        $this->db->where('d.doctor_show_status', 'active');
        $this->db->where('ds.featured_status', 1);

        // Exclude doctors already subscribed by this specific user
        if ($exclude_user_id) {
            $this->db->where("d.id NOT IN (SELECT doctor_id FROM user_subscribed_doctors usd JOIN user_subscriptions us ON us.id = usd.subscription_id WHERE us.user_id = '$exclude_user_id' AND usd.status = 'active' AND us.status = 'active')", NULL, FALSE);
        }

        $this->db->group_by('d.id');
        $doctors = $this->db->get()->result();

        foreach ($doctors as $doc) {
            // Mapping for doctor list response
            $doc->doctor_image = !empty($doc->doctor_image) ? base_url() . 'uploads/doctors/' . $doc->doctor_image : base_url() . 'uploads/profile-icon-3.png';
            $doc->specialisation_name = $this->get_specialisation_name($doc->specialisation);
            $designation_names = $this->get_designation_names($doc->designations);
            $doc->designation_names = implode(',', $designation_names);
            $doc->rating_count = !empty($doc->rating_count) ? $doc->rating_count : "0";
            $doc->blue_tick = ($doc->blue_tick == 1 || $doc->blue_tick == 'active') ? 'active' : 'inactive';
        }

        return $doctors;
    }

    public function get_subscribed_doctor_details($doctor_id)
    {
        $this->db->select('d.*, ds.status as subscription_status, ds.start_at, ds.end_at, ds.featured_status, sp.name as subscription_plan_name, sp.price as plan_price');
        $this->db->from('doctors d');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id', 'left');
        $this->db->join('subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id', 'left');
        $this->db->where('d.id', $doctor_id);
        $doctor = $this->db->get()->row();

        if ($doctor) {
            // ... (keeping previous logic)
            $doctor->doctor_image = !empty($doctor->doctor_image) ? base_url() . 'uploads/doctors/' . $doctor->doctor_image : base_url() . 'uploads/profile-icon-3.png';
            $doctor->cover_image = !empty($doctor->hospital_image) ? base_url() . 'uploads/doctors/' . $doctor->hospital_image : '';
            $doctor->digital_signature = !empty($doctor->digital_signature) ? base_url() . 'uploads/doctors/' . $doctor->digital_signature : '';

            // Specialisation name instead of ID
            $specialisation_id = $doctor->specialisation;
            $doctor->specialisation = $this->get_specialisation_name($specialisation_id);

            // Specialist in (Array)
            $doctor->specialist_in = !empty($doctor->specialist_in) ? explode(',', $doctor->specialist_in) : [];

            // License
            $doctor->license = $doctor->doctor_license_no ?? '';

            // Designations (Array)
            $doctor->designations = $this->get_designation_names($doctor->designations);

            // Fees
            $doctor->voice_call = $doctor->voice_call ?? "0";
            $doctor->video_call = $doctor->video_call ?? "0";
            $doctor->chat_price = $doctor->chat_price ?? "0";

            $doctor->category_name = []; // As per example JSON
            $doctor->doctor_rating = $doctor->rating;
            $doctor->total_users_reviewed = $doctor->rating_count;

            // Calculate remaining days as Date Range
            if (!empty($doctor->end_at)) {
                $formatted_start = !empty($doctor->start_at) ? date('d M Y', strtotime($doctor->start_at)) : '';
                $formatted_end = date('d M Y', strtotime($doctor->end_at));
                $doctor->remaining_days = $formatted_end;
            }
            else {
                $doctor->remaining_days = "No active plan";
            }

            // Cleanup
            unset($doctor->password);
            unset($doctor->doctor_license_no);
            unset($doctor->end_at);
        }

        return $doctor;
    }

    private function get_specialisation_name($id)
    {
        if (empty($id))
            return '';
        $res = $this->db->select('name')->where('id', $id)->get('specialisation')->row();
        return $res ? $res->name : '';
    }

    private function get_designation_names($ids)
    {
        if (empty($ids))
            return [];
        $id_array = explode(',', $ids);
        $res = $this->db->select('name')->where_in('id', $id_array)->get('designations')->result();
        $names = [];
        foreach ($res as $row) {
            $names[] = $row->name;
        }
        return $names;
    }
    public function get_subscription_terms()
    {
        $this->db->where('status', 1);
        return $this->db->get('subscription_terms')->result();
    }
}