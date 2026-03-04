<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --navy: #0B1437; --navy-2: #132053; --teal: #0FC6C2; --teal-2: #0AABA8; --amber: #F59E0B; --rose: #F43F5E; --green: #10B981; --slate: #64748B; --light: #F0F4FF; --white: #FFFFFF; --card: #FFFFFF; --border: #E2E8F0; --shadow: 0 4px 24px rgba(11, 20, 55, .10); --shadow-lg: 0 12px 40px rgba(11, 20, 55, .18); --radius: 16px; --radius-sm: 10px; }
  body { font-family: 'DM Sans', sans-serif; background: #EFF3FC; color: var(--navy); min-height: 100vh; }
  .topbar { background: var(--white); border-bottom: 1px solid var(--border); padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 12px rgba(11, 20, 55, .06); }
  .logo { display: flex; align-items: center; gap: 10px; }
  .logo-icon { width: 38px; height: 38px; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
  .logo-text { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 17px; color: var(--navy); letter-spacing: -.3px; }
  .logo-text span { color: var(--teal); }
  .nav-tabs { display: flex; gap: 4px; }
  .nav-tab { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: none; background: transparent; color: var(--slate); transition: all .2s; font-family: 'DM Sans', sans-serif; text-decoration: none; display: inline-flex; align-items: center; }
  .nav-tab:hover { background: var(--light); color: var(--navy); }
  .nav-tab.active { background: var(--navy); color: var(--white); }
  .topbar-right { display: flex; align-items: center; gap: 12px; }
  .avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--teal) 0%, var(--navy) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 13px; cursor: pointer; }
  .notif-btn { width: 36px; height: 36px; border-radius: 50%; background: var(--light); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; font-size: 16px; text-decoration: none; color: inherit; }
  .notif-badge { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; background: var(--rose); border-radius: 50%; border: 2px solid white; }
  .page { max-width: 1180px; margin: 0 auto; padding: 32px 24px; }
  .page-header { margin-bottom: 28px; }
  .page-title { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: var(--navy); }
  .page-sub { font-size: 14px; color: var(--slate); margin-top: 4px; }
  .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
  .stat-card { background: var(--card); border-radius: var(--radius); padding: 22px 20px; box-shadow: var(--shadow); position: relative; overflow: hidden; border: 1px solid var(--border); transition: transform .2s, box-shadow .2s; }
  .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
  .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
  .stat-card.blue::before { background: var(--navy); }
  .stat-card.teal::before { background: var(--teal); }
  .stat-card.green::before { background: var(--green); }
  .stat-card.amber::before { background: var(--amber); }
  .stat-icon { font-size: 28px; margin-bottom: 10px; }
  .stat-value { font-family: 'Syne', sans-serif; font-size: 34px; font-weight: 800; color: var(--navy); }
  .stat-label { font-size: 13px; color: var(--slate); margin-top: 2px; font-weight: 500; }
  .stat-change { font-size: 12px; margin-top: 8px; font-weight: 600; }
  .stat-change.up { color: var(--green); }
  .stat-change.down { color: var(--rose); }
  .dash-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
  .table-card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
  .card-header { padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
  .card-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; color: var(--navy); }
  .card-action { font-size: 13px; color: var(--teal); font-weight: 600; cursor: pointer; border: none; background: none; font-family: 'DM Sans', sans-serif; padding: 6px 12px; border-radius: 6px; transition: background .2s; text-decoration: none; }
  .card-action:hover { background: #e0fafa; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--slate); background: var(--light); }
  td { padding: 13px 16px; font-size: 13.5px; border-bottom: 1px solid #F1F5F9; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #FAFCFF; }
  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
  .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
  .badge.found { background: #ECFDF5; color: #059669; }
  .badge.found .badge-dot { background: #059669; }
  .badge.returned { background: #EFF6FF; color: #2563EB; }
  .badge.returned .badge-dot { background: #2563EB; }
  .badge.unclaimed { background: #FFF7ED; color: #D97706; }
  .badge.unclaimed .badge-dot { background: #D97706; }
  .obj-name { display: flex; align-items: center; gap: 9px; }
  .obj-emoji { font-size: 18px; width: 32px; height: 32px; background: var(--light); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
  .right-col { display: flex; flex-direction: column; gap: 18px; }
  .activity-card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); }
  .activity-list { padding: 4px 0; }
  .activity-item { padding: 12px 20px; display: flex; align-items: flex-start; gap: 12px; border-bottom: 1px solid #F1F5F9; transition: background .15s; }
  .activity-item:last-child { border-bottom: none; }
  .activity-item:hover { background: #FAFCFF; }
  .activity-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
  .activity-dot.teal { background: var(--teal); }
  .activity-dot.green { background: var(--green); }
  .activity-dot.amber { background: var(--amber); }
  .activity-text { font-size: 13px; line-height: 1.5; color: var(--navy); }
  .activity-time { font-size: 11px; color: var(--slate); margin-top: 2px; }
  .chart-card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); }
  .chart-bars { padding: 12px 20px 18px; display: flex; align-items: flex-end; gap: 10px; height: 140px; }
  .bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end; }
  .bar { width: 100%; border-radius: 5px 5px 0 0; transition: opacity .2s; }
  .bar:hover { opacity: .8; }
  .bar-label { font-size: 10px; color: var(--slate); font-weight: 600; }
  .toolbar { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
  .search-box { flex: 1; min-width: 220px; position: relative; }
  .search-box input { width: 100%; padding: 10px 16px 10px 40px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; font-family: 'DM Sans', sans-serif; background: white; outline: none; color: var(--navy); transition: border-color .2s; }
  .search-box input:focus { border-color: var(--teal); }
  .search-box::before { content: '🔍'; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px; }
  .filter-select { padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13.5px; font-family: 'DM Sans', sans-serif; background: white; outline: none; color: var(--navy); cursor: pointer; }
  .btn { padding: 10px 20px; border-radius: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; display: inline-flex; align-items: center; gap: 7px; transition: all .2s; text-decoration: none; }
  .btn-primary { background: var(--navy); color: white; }
  .btn-primary:hover { background: var(--navy-2); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(11, 20, 55, .25); }
  .btn-teal { background: var(--teal); color: white; }
  .btn-teal:hover { background: var(--teal-2); }
  .btn-outline { background: white; color: var(--navy); border: 1.5px solid var(--border); }
  .btn-outline:hover { border-color: var(--navy); }
  .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
  .item-card { background: white; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; transition: transform .2s, box-shadow .2s; }
  .item-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
  .item-img { height: 120px; display: flex; align-items: center; justify-content: center; font-size: 52px; position: relative; }
  .item-img.bg1 { background: linear-gradient(135deg, #E0F2FE, #BFDBFE); }
  .item-img.bg2 { background: linear-gradient(135deg, #D1FAE5, #A7F3D0); }
  .item-img.bg3 { background: linear-gradient(135deg, #FEF3C7, #FDE68A); }
  .item-img.bg4 { background: linear-gradient(135deg, #FCE7F3, #FBCFE8); }
  .item-img.bg5 { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); }
  .item-img.bg6 { background: linear-gradient(135deg, #FFEDD5, #FED7AA); }
  .item-badge-pos { position: absolute; top: 10px; right: 10px; }
  .item-body { padding: 16px; }
  .item-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 15px; color: var(--navy); margin-bottom: 5px; }
  .item-meta { font-size: 12.5px; color: var(--slate); display: flex; flex-direction: column; gap: 3px; }
  .item-meta span { display: flex; align-items: center; gap: 5px; }
  .item-footer { padding: 12px 16px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
  .claim-btn { font-size: 12.5px; font-weight: 600; color: var(--teal); cursor: pointer; border: 1.5px solid var(--teal); border-radius: 7px; padding: 5px 12px; background: none; font-family: 'DM Sans', sans-serif; transition: all .2s; text-decoration: none; }
  .claim-btn:hover { background: var(--teal); color: white; }
  .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(11, 20, 55, .45); z-index: 200; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
  .modal-overlay.open { display: flex; }
  .modal { background: white; border-radius: 20px; width: 100%; max-width: 540px; box-shadow: 0 24px 80px rgba(11, 20, 55, .3); overflow: hidden; }
  .modal-header { padding: 24px 28px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
  .modal-title { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 800; color: var(--navy); }
  .modal-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: var(--light); cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: background .2s; }
  .modal-close:hover { background: #e2e8f0; }
  .modal-body { padding: 24px 28px; }
  .modal-footer { padding: 16px 28px 24px; display: flex; gap: 10px; justify-content: flex-end; }
  .detail-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }
  .detail-main { display: flex; flex-direction: column; gap: 20px; }
  .detail-hero { background: linear-gradient(135deg, #EFF3FC 0%, #E0F2FE 100%); border-radius: var(--radius); height: 200px; display: flex; align-items: center; justify-content: center; font-size: 80px; border: 1px solid var(--border); }
  .info-card { background: white; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); padding: 22px; }
  .info-card h3 { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; color: var(--navy); margin-bottom: 14px; }
  .info-row { display: flex; align-items: flex-start; gap: 14px; padding: 10px 0; border-bottom: 1px solid #F1F5F9; }
  .info-row:last-child { border-bottom: none; }
  .info-icon { font-size: 18px; width: 36px; height: 36px; background: var(--light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .info-label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: var(--slate); }
  .info-value { font-size: 14px; color: var(--navy); font-weight: 500; margin-top: 2px; }
  .action-card { background: white; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); padding: 22px; }
  .action-card h4 { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700; color: var(--navy); margin-bottom: 14px; }
  .action-btn-group { display: flex; flex-direction: column; gap: 10px; }
  .action-btn-item { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; text-align: left; display: flex; align-items: center; gap: 10px; transition: all .2s; width: 100%; }
  .action-btn-item.primary { background: var(--navy); color: white; }
  .action-btn-item.primary:hover { background: var(--navy-2); }
  .action-btn-item.success { background: #ECFDF5; color: #059669; }
  .action-btn-item.success:hover { background: #d1fae5; }
  .action-btn-item.danger { background: #FFF1F2; color: var(--rose); }
  .action-btn-item.danger:hover { background: #ffe4e6; }
  .login-wrap { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
  .login-left { background: linear-gradient(160deg, var(--navy) 0%, var(--navy-2) 100%); display: flex; align-items: center; justify-content: center; padding: 60px; position: relative; overflow: hidden; }
  .login-left::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 70% 80%, rgba(15, 198, 194, .18) 0%, transparent 60%), radial-gradient(circle at 20% 30%, rgba(255, 255, 255, .05) 0%, transparent 50%); }
  .login-left-content { position: relative; z-index: 1; text-align: center; color: white; }
  .login-logo { font-size: 60px; margin-bottom: 20px; }
  .login-hero-title { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800; line-height: 1.2; margin-bottom: 14px; }
  .login-hero-sub { font-size: 15px; opacity: .75; line-height: 1.6; max-width: 320px; }
  .login-feat { display: flex; flex-direction: column; gap: 14px; margin-top: 32px; text-align: left; }
  .login-feat-item { display: flex; align-items: center; gap: 12px; font-size: 13.5px; opacity: .85; }
  .login-feat-icon { width: 32px; height: 32px; background: rgba(255, 255, 255, .12); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
  .login-right { background: var(--light); display: flex; align-items: center; justify-content: center; padding: 60px; }
  .login-form-wrap { width: 100%; max-width: 380px; }
  .login-form-title { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: var(--navy); margin-bottom: 6px; }
  .login-form-sub { font-size: 14px; color: var(--slate); margin-bottom: 32px; }
  .login-role-select { display: flex; gap: 10px; margin-bottom: 24px; }
  .role-btn { flex: 1; padding: 10px; border-radius: 10px; border: 2px solid var(--border); background: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; color: var(--slate); transition: all .2s; display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .role-btn.active { border-color: var(--teal); color: var(--navy); background: #f0fdfb; }
  .role-btn span { font-size: 20px; }
  .forgot { font-size: 13px; color: var(--teal); text-align: right; cursor: pointer; margin-top: -12px; margin-bottom: 20px; }
  .notif-list { display: flex; flex-direction: column; gap: 12px; max-width: 680px; }
  .notif-item { background: white; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); padding: 18px 20px; display: flex; gap: 16px; align-items: flex-start; transition: transform .2s; }
  .notif-item:hover { transform: translateX(4px); }
  .notif-item.unread { border-left: 4px solid var(--teal); }
  .notif-icon-wrap { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .notif-icon-wrap.match { background: #ECFDF5; }
  .notif-icon-wrap.info { background: #EFF6FF; }
  .notif-icon-wrap.alert { background: #FFF7ED; }
  .notif-content { flex: 1; }
  .notif-title { font-weight: 700; font-size: 14px; color: var(--navy); margin-bottom: 4px; }
  .notif-desc { font-size: 13px; color: var(--slate); line-height: 1.5; }
  .notif-time { font-size: 11.5px; color: var(--slate); margin-top: 6px; }
  .unread-dot { width: 8px; height: 8px; background: var(--teal); border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
  .screen-label { background: #E0F2FE; color: #0369A1; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
  .form-group { margin-bottom: 18px; }
  .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
  .form-input, .form-select, .form-textarea { width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; font-family: 'DM Sans', sans-serif; color: var(--navy); outline: none; transition: border-color .2s; background: white; }
  .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--teal); }
  .form-textarea { resize: vertical; min-height: 80px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .upload-zone { border: 2px dashed var(--border); border-radius: 10px; padding: 28px; text-align: center; cursor: pointer; transition: all .2s; }
  .upload-zone:hover { border-color: var(--teal); background: #f0fdfb; }
  .upload-icon { font-size: 32px; margin-bottom: 8px; }
  .upload-text { font-size: 13px; color: var(--slate); }
  .upload-text strong { color: var(--teal); }
  .text-danger { color: #dc2626; font-size: 12px; margin-top: 5px; display: block; }
  @media (max-width: 900px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .dash-grid { grid-template-columns: 1fr; }
    .detail-grid { grid-template-columns: 1fr; }
    .login-wrap { grid-template-columns: 1fr; }
    .login-left { display: none; }
    .topbar { padding: 0 12px; }
    .nav-tab { padding: 8px 10px; font-size: 12px; }
    .form-row { grid-template-columns: 1fr; }
    .modal { margin: 20px; max-width: none; }
  }
</style>
