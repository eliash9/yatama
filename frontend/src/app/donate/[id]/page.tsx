"use client";
import { useEffect, useState } from "react";
import { createDonation } from "@/features/donations/donations";
import { getPaymentOptions, PaymentOptions } from "@/features/donations/payments";
import { useRouter } from "next/navigation";

export default function DonatePage({ params }: { params: { id: string } }) {
  // amount = nominal dasar yang dimasukkan user (tanpa kode unik)
  const [amount, setAmount] = useState<number>(50000);
  const [isMounted, setIsMounted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<string | null>(null);
  const [note, setNote] = useState<string>("");
  const [channel, setChannel] = useState<'transfer'|'qris'|'ewallet'>('transfer');
  const [provider, setProvider] = useState<string>("BCA");
  const [opts, setOpts] = useState<PaymentOptions| null>(null);
  // uniqueCode hanya digunakan untuk channel transfer (3 digit). Inisialisasi setelah mount untuk hindari mismatch SSR.
  const [uniqueCode, setUniqueCode] = useState<number>(0);
  const router = useRouter();

  useEffect(() => {
    getPaymentOptions().then(setOpts).catch(()=>setOpts(null));
  }, []);

  useEffect(() => {
    setIsMounted(true);
    // generate kode unik pertama kali setelah mount
    setUniqueCode(Math.floor(Math.random()*999)+1);
  }, []);

  useEffect(() => {
    if (channel === 'transfer') {
      const first = Object.keys(opts?.banks || {})[0];
      if (first) setProvider(first);
    } else if (channel === 'ewallet') {
      const first = Object.keys(opts?.ewallets || {})[0];
      if (first) setProvider(first);
    } else {
      setProvider('');
    }
  }, [channel, opts]);

  // Helper untuk memformat 3 digit dengan leading zero
  const code3 = Math.max(0, uniqueCode).toString().padStart(3, '0');
  const totalWithCode = channel === 'transfer' && uniqueCode > 0 ? (amount + uniqueCode) : amount;

  function regenerateCode() {
    // ganti kode unik, tetap dalam rentang 1..999
    let next = Math.floor(Math.random()*999)+1;
    if (next === uniqueCode) {
      next = (next % 999) + 1;
    }
    setUniqueCode(next);
  }

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true); setMessage(null);
    const payload: any = {
      campaign_id: Number(params.id),
      amount: totalWithCode,
      donor_note: note || undefined,
      payment_channel: channel,
      provider: provider || undefined,
    };
    if (channel === 'transfer') {
      payload.base_amount = amount;
      payload.client_unique_suffix = uniqueCode;
      payload.use_unique_suffix = true;
    }
    const res = await createDonation(payload);
    setLoading(false);
    if (res.ref) {
      router.push(`/pay/${encodeURIComponent(res.ref)}`);
      return;
    }
    setMessage('Donasi dibuat. Mengarahkan ke instruksi pembayaran...');
  }

  return (
    <div className="max-w-xl mx-auto space-y-4">
      <h1 className="text-2xl font-semibold">Donasi Kampanye #{params.id}</h1>
      <form onSubmit={onSubmit} className="space-y-4 bg-white p-4 border rounded-md">
        <div>
          <label className="block text-sm mb-1">Nominal</label>
          <input className="w-full border rounded-md p-2" type="number" min={10000} value={amount} onChange={(e)=>setAmount(Number(e.target.value))} />
          <div className="mt-2 flex gap-2 flex-wrap text-sm">
            {[50000,100000,200000,500000].map(v=> (
              <button key={v} type="button" className="px-3 py-1 border rounded-md hover:bg-gray-50" onClick={()=>setAmount(v)}>Rp{v.toLocaleString('id-ID')}</button>
            ))}
          </div>
          {channel === 'transfer' && isMounted && uniqueCode > 0 && (
            <div className="mt-3 text-sm bg-blue-50 border border-blue-200 rounded p-2">
              <div className="flex items-center justify-between">
                <span>Total transfer (termasuk kode unik {code3})</span>
                <button type="button" onClick={regenerateCode} className="text-xs px-2 py-0.5 border rounded">Ganti kode</button>
              </div>
              <div className="mt-1 text-lg font-semibold">Rp{totalWithCode.toLocaleString('id-ID')}</div>
            </div>
          )}
        </div>

        <div>
          <label className="block text-sm mb-1">Metode Pembayaran</label>
          <div className="flex gap-3 text-sm">
            {(['transfer','qris','ewallet'] as const).map(ch => (
              <label key={ch} className="inline-flex items-center gap-2">
                <input type="radio" name="channel" value={ch} checked={channel===ch} onChange={()=>setChannel(ch)} />
                <span className="capitalize">{ch}</span>
              </label>
            ))}
          </div>
        </div>

        {(channel==='transfer' && Object.keys(opts?.banks||{}).length>0) && (
          <div>
            <label className="block text-sm mb-1">Bank</label>
            <select className="w-full border rounded-md p-2" value={provider} onChange={e=>setProvider(e.target.value)}>
              {Object.keys(opts!.banks).map(key => (
                <option key={key} value={key}>{key}</option>
              ))}
            </select>
          </div>
        )}

        {(channel==='ewallet' && Object.keys(opts?.ewallets||{}).length>0) && (
          <div>
            <label className="block text-sm mb-1">E-Wallet</label>
            <select className="w-full border rounded-md p-2" value={provider} onChange={e=>setProvider(e.target.value)}>
              {Object.keys(opts!.ewallets).map(key => (
                <option key={key} value={key}>{key}</option>
              ))}
            </select>
          </div>
        )}

        <div>
          <label className="block text-sm mb-1">Catatan (opsional)</label>
          <textarea className="w-full border rounded-md p-2" rows={3} value={note} onChange={(e)=>setNote(e.target.value)} />
        </div>

        {message && <div className="text-sm p-2 bg-green-50 border border-green-200 rounded">{message}</div>}

        <button disabled={loading} className="w-full bg-primary text-white rounded-md p-2">{loading? 'Memproses...' : 'Donasi Sekarang'}</button>
      </form>

      {/* Instruksi dipindahkan ke halaman terpisah /pay/[ref] */}
    </div>
  );
}
