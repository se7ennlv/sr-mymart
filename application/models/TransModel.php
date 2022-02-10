<?php

class TransModel extends CI_Model
{
    public function FindDocNo()
    {
        $sql = "SELECT CONCAT('INV', RIGHT('000000'+ CAST((ISNULL(MAX(TranID),0) + 1) AS VARCHAR), 6)) AS [DocNo] FROM Transactions";
        $query = $this->db->query($sql);

        return $query->row();
    }

    public function FindAllPaymentType()
    {
        $this->db->where('PaymtIsActive', 1);
        $this->db->where_in('PaymtCode', array('CASH', 'CREDIT'));
        $query = $this->db->get('PaymentTypes');
        return $query->result();
    }

    public function ExecuteInsertTrans()
    {
        $paymtCode = $this->input->post('paymtCode');
        $totalCredit = 0;

        if ($paymtCode == 'CASH') {
            $isPaid = 1;
            $totalCredit = 0;
        } else {
            $isPaid = 0;
            $totalCredit = $this->input->post('afterDis');
        }

        $data = array(
            'TranDocNo' => $this->input->post('docNo'),
            'TranCustID' => $this->input->post('custId'),
            'TranCustName' => $this->input->post('custName'),
            'TranTotalAmount' => $this->input->post('amount'),
            'TranTotalPaid' => $this->input->post('paid'),
            'TranTotalCredit' => $totalCredit,
            'TranDiscPercent' => $this->input->post('disPercent'),
            'TranDiscMoney' => $this->input->post('disMoney'),
            'TranAfterDisc' => $this->input->post('afterDis'),
            'TranChangeAmount' => $this->input->post('changeAmount'),
            'TranIsPaid' => $isPaid,
            'TranPaymentCode' => $paymtCode,
            'TranCreatedBy' => $this->session->userdata('username'),
            'TranDocNo' => $this->input->post('docNo')
        );

        $this->db->insert('Transactions', $data);
        $this->output->set_content_type('application/json')->set_output(json_encode(array("status" => "success", "message" => 'Tender success')));
    }

    public function ExecuteInsertDetailSales()
    {
        $data = array(
            'DSDocNo' => $this->input->post('docNo'),
            'DSItemID' => $this->input->post('itemId'),
            'DSItemName' => $this->input->post('itemName'),
            'DSItemPrice' => $this->input->post('itemPrice'),
            'DSItemQty' => $this->input->post('itemQty'),
            'DSTotalPrice' => $this->input->post('totalSales')
        );

        $this->db->insert('DetailSales', $data);
    }

    public function ExecuteCutStock()
	{
		$saleQty = $this->input->post('saleQty');

        $this->db->set('ItemStock', "ItemStock - $saleQty", FALSE);
		$this->db->where('ItemID', $this->input->post('itemId'));
		$this->db->update('Items');
		$this->output->set_content_type('application/json')->set_output(json_encode(array("status" => "success", "message" => 'Updated.')));
	}



    
}