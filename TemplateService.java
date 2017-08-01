@Service
public class TemplateService {
    @Autowired
    private TemplateDao templateDao;

    List<Template> getTemplateList(HashMap map) {
        return TemplateDao.getList(map);
    }

    List<Template> getSearchResult(HashMap map) {
        return TemplateDao.getSearchResult(map);
    }

    Template findTemplateByTemplateId(int templateId) {
        return TemplateDao.findTemplateByTemplateId(templateId);
    }

    void delete(int templateId) {
        TemplateDao.delete(templateId);
    }

    void deleteByPkIds(List<Integer> ids) {
        TemplateDao.deleteByPkIds(ids);
    }

    void update(Template template) {
        TemplateDao.update(template);
    }

    int insert(Template template) {
        return TemplateDao.insert(template);
    }

    int getCount(Template template) {
        return TemplateDao.getCount(template);
    }

    int getTotal() {
        return TemplateDao.getTotal();
    }
}