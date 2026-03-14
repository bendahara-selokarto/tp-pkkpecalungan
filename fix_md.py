from pathlib import Path
import re

def fix_file(path):
    text = path.read_text(encoding='utf-8')
    lines = text.splitlines()
    out_lines=[]
    i=0
    while i < len(lines):
        line=lines[i]
        if re.match(r'^(#{1,6})\s', line):
            if i>0 and out_lines and out_lines[-1].strip()!='':
                out_lines.append('')
            out_lines.append(line)
            if i+1 < len(lines) and lines[i+1].strip()!='' and not re.match(r'^(#{1,6})\s', lines[i+1]):
                out_lines.append('')
            i += 1
            continue
        if line.strip().startswith('|') and '---' in line:
            parts=[p.strip() for p in line.strip().strip('|').split('|')]
            line='| ' + ' | '.join(parts) + ' |'
            out_lines.append(line)
            i +=1
            continue
        if line.strip().startswith('|') and '|' in line:
            parts=[p.strip() for p in line.strip().strip('|').split('|')]
            line='| ' + ' | '.join(parts) + ' |'
            out_lines.append(line)
            i +=1
            continue
        out_lines.append(line)
        i += 1
    final=[]
    in_table=False
    for line in out_lines:
        if line.strip().startswith('|'):
            if not in_table and final and final[-1].strip()!='':
                final.append('')
            in_table=True
            final.append(line)
        else:
            if in_table and line.strip()!='':
                final.append('')
            in_table=False
            final.append(line)
    path.write_text('\n'.join(final)+'\n', encoding='utf-8')

for p in Path('docs/domain').glob('*.md'):
    fix_file(p)
    print('fixed', p)
