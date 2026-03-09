# Discussion Notes

Status: `non-canonical`  
Indexed: `no`  
Purpose: catatan diskusi ad hoc dengan agent.

Entry point keyword: `diskusi-md`

Aturan:

- Folder ini tidak menjadi source of truth proses, domain, atau arsitektur.
- Jangan pakai file di folder ini sebagai dasar implementasi final tanpa promosi ke artefak canonical yang sesuai.
- Jika hasil diskusi sudah menjadi keputusan final, pindahkan ringkasannya ke `docs/process/TODO_*.md`, `docs/adr/ADR_*.md`, atau dokumen canonical lain yang relevan.
- Hindari menaruh checklist status concern resmi di folder ini.
- Saat user menulis `diskusi-md`, agent mengarahkan konteks ke folder `docs/discussion/` atau file diskusi aktif yang dirujuk user.
- Untuk diskusi istilah, agent membaca `docs/discussion/vocabulary/VOCABULARY_INDEX.md` terlebih dahulu, lalu membuka annex yang relevan secara scoped.

Template awal tersedia di `TEMPLATE_AGENT_DISCUSSION.md`.

Struktur vocabulary diskusi:

- `docs/discussion/vocabulary/VOCABULARY_INDEX.md`
  - index tipis istilah kerja.
- `docs/discussion/vocabulary/VOCAB_FLOW.md`
  - istilah flow dan concern.
- `docs/discussion/vocabulary/VOCAB_PRODUCT_STRUCTURE.md`
  - istilah struktur produk dan UI.
- `docs/discussion/vocabulary/VOCAB_DATA_TECHNICAL.md`
  - istilah data dan teknis.
